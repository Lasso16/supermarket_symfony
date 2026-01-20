<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    #[Route('/sobre-nosotros', name: 'app_sobre_nosotros')]
    public function sobreNosotros(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }
        return $this->render('pages/sobre_nosotros.html.twig');
    }

    #[Route('/contacto', name: 'app_contacto')]
    public function contacto(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }
        return $this->render('pages/contacto.html.twig');
    }

    #[Route('/blog', name: 'app_blog')]
    public function blog(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }
        return $this->render('pages/blog.html.twig');
    }
}
