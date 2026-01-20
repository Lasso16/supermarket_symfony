<?php

namespace App\Controller;

use App\Entity\Categoria;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoriaController extends AbstractController
{
    #[Route('/categoria', name: 'app_categoria')]
    public function index(ManagerRegistry $doctrine): Response
    {
        // Obtener todas las categorías desde la base de datos
        $categorias = $doctrine->getRepository(Categoria::class)->findAll();

        return $this->render('categoria/index.html.twig', [
            'categorias' => $categorias,
        ]);
    }
}
