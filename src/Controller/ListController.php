<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\Storage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\File as FileConstraints;

#[Route('/file')]
class ListController extends AbstractController
{
    protected EntityManagerInterface $em;
    protected SluggerInterface $slugger;
    protected Filesystem $filesystem;

    public function __construct(
        EntityManagerInterface $em,
        SluggerInterface       $slugger,
        Filesystem             $filesystem,
    ) {
        $this->em = $em;
        $this->slugger = $slugger;
        $this->filesystem = $filesystem;
    }

    #[Route('/list/{id}', name: 'app_list')]
    public function index(Request $request, int $id = null): Response
    {
        /** @var Storage $storage */
        if($id && $this->isGranted('ROLE_ADMIN')) {
            $storage = $this->em->getRepository(Storage::class)->findOneBy(["user" => $id]);
        } else {
            $storage = $this->em->getRepository(Storage::class)->findOneBy(["user" => $this->getUser()]);
        }

        $form = $this->createFormBuilder()
            ->add('file', FileType::class, [
                'label' => 'Fichier',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new FileConstraints([
                        'maxSize' => $storage->getLeftCapacity(),
                        'maxSizeMessage' => 'Le fichier est trop volumineux. La capacité restante est de ' . $storage->getLeftCapacity() . ' Go.',
                    ])
                ],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->get('file')->getData();
          
            if (!$data) {
               $this->addFlash('danger', "Erreur lors de l'upload du fichier");
               return $this->redirectToRoute('app_list');
            }
          
            if ($storage->getLeftCapacity() < $data->getSize()) {
                $this->addFlash('danger', "Vous n'avez plus assez de place dans votre espace de stockage.");
                return $this->redirectToRoute('app_list');
            }

            $fileName = $data->getClientOriginalName();
            $fileName = str_replace(' - ', '-', $fileName);
            $fileName = str_replace(' ', '-', $fileName);

            $file = new File();
            $file->setName($fileName);
            $file->setSize($data->getSize());
            $file->setFormat(pathinfo($fileName, PATHINFO_EXTENSION));
            $file->setUploadDate(new \DateTime());
            $file->setName($fileName);
            $file->setStorage($storage);

            $data->move($this->getParameter('upload_directory'), $fileName);

            $storage->setLeftCapacity($storage->getLeftCapacity() - $file->getSize());

            $this->em->persist($storage);
            $this->em->persist($file);
            $this->em->flush();

            $this->addFlash('success', 'Fichier téléchargé avec succès !');
            return $this->redirectToRoute('app_list');
        }

        return $this->render('list/index.html.twig', [
            'form' => $form,
            'storage' => $storage,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_delete_file')]
    public function delete(int $id): Response
    {
        $file = $this->em->getRepository(File::class)->find($id);
        $storage = $file->getStorage();

        if (!$file) {
            $this->addFlash('warning', "fichier introuvable");
            return $this->redirectToRoute('app_list');
        }

        try {
            $this->em->remove($file);
            $this->filesystem->remove($this->getParameter('upload_directory') . '/' . $file->getName());
            $storage->setLeftCapacity($storage->getLeftCapacity() + $file->getSize());
            $this->em->flush();

            $this->addFlash('success', 'Fichier supprimé avec succès !');
        } catch (\Exception $e) {
            $this->addFlash('danger', $e->getMessage());
            return $this->redirectToRoute('app_list');
        }

        return $this->redirectToRoute('app_list');
    }

    #[Route('/download/{id}', name: 'app_download_file')]
    public function download(int $id): Response
    {
        $file = $this->em->getRepository(File::class)->find($id);

        if (!$file) {
            $this->addFlash('warning', "fichier introuvable");
            return $this->redirectToRoute('app_list');
        }

        $filePath = $this->getParameter('upload_directory') . "/" . $file->getName();

        if (!file_exists($filePath)) {
            $this->addFlash('warning', "Le fichier demandé n\'existe pas sur le serveur.");
            return $this->redirectToRoute('app_list');
        }

        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $file->getName());

        $this->addFlash('success', 'Téléchargement du fichier initié avec succès !');
        return $response;
    }
}
