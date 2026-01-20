<?php

namespace App\Controller;

use App\BLL\ProductoBLL;
use App\Entity\Producto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductoController extends AbstractController
{
    #[Route('/producto', name: 'app_producto')]
    public function index(ProductoBLL $productoBLL): Response
    {
        return $this->render('producto/index.html.twig', [
            'productos' => $productoBLL->getAll(),
        ]);
    }

    #[Route('/producto/detalle/{id}', name: 'app_producto_show')]
    public function show(int $id, ProductoBLL $productoBLL): Response
    {
        $productos = $productoBLL->getAll();
        $producto = null;

        foreach ($productos as $p) {
            if ($p->getId() === $id) {
                $producto = $p;
                break;
            }
        }

        if (!$producto) {
            throw $this->createNotFoundException('Producto no encontrado');
        }

        return $this->render('producto/show.html.twig', [
            'producto' => $producto,
        ]);
    }

    #[Route('/producto/{id}', name: 'app_producto_delete_json', methods: ['DELETE'])]
    public function deleteJson(Producto $producto, ProductoBLL $productoBLL): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /** @var \App\Entity\Usuario $user */
        $user = $this->getUser();

        $productoBLL->delete($producto, $user, true);
        return new JsonResponse(['eliminado' => true]);
    }
}
