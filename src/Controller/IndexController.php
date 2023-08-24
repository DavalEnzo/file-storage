<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route(path: '/', name: 'index')]
    public function index(): RedirectResponse|Response
    {
        if($this->getUser()){
            if($this->getUser()->getStatus() == 1){
                return $this->redirectToRoute('app_list');
            } else if ($this->getUser()->getStatus() == 0) {
                return $this->redirectToRoute('achat');
            }
        }
        return $this->render('homepage/index.html.twig');
    }

    /**
     * @Route("/achat", name="achat")
     */
    public function achat(): Response
    {
        return $this->render('paiement/achat.html.twig');
    }
    
}
