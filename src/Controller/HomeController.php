<?php

namespace App\Controller;

use App\BLL\CategoriaBLL;
use App\BLL\ProductoBLL;
use App\Entity\Producto;
use App\Entity\Categoria;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, ProductoBLL $productoBLL, CategoriaBLL $categoriaBLL): Response
    {
        // Extract filters from request
        $filters = [
            'categoria' => $request->query->get('categoria'),
            'fecha_desde' => $request->query->get('fecha_desde'),
            'fecha_hasta' => $request->query->get('fecha_hasta'),
            'search' => $request->query->get('search'),
            'usuario' => $request->query->get('usuario'),
        ];

        // Get filtered products via BLL
        $productos = $productoBLL->getAll($filters);

        // Get categories for the filter dropdown
        $categorias = $categoriaBLL->getAll();

        return $this->render('index.view.html.twig', [
            'productos' => $productos,
            'categorias' => $categorias,
            'filters' => $filters,
        ]);
    }
}
