<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\InscriptionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('base.html.twig');
    }

    /**
     * @Route("/sign-in", name="sign-in")
     */
    public function new(Request $request,EntityManagerInterface $entityManager) : Response
    {

        $user = new User();
        $form = $this->createForm(InscriptionType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){ 
            
            
             
            $user = $form->getData ();
            

            
           
            $entityManager->persist($user);
            $entityManager->flush();


            return $this->redirectToRoute('index');
        }

        return $this->render('/sign-in/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}