<?php

namespace App\Controller;

use App\Repository\ProductoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/carrito')]
class CarritoController extends AbstractController
{
    #[Route('/', name: 'app_carrito')]
    public function index(SessionInterface $session, ProductoRepository $productoRepository): Response
    {
        $carrito = $session->get('carrito', []);
        $items = [];
        $total = 0;

        foreach ($carrito as $id => $cantidad) {
            $producto = $productoRepository->find($id);
            if ($producto) {
                $subtotal = (float)$producto->getPrecio() * $cantidad;
                $items[] = [
                    'producto' => $producto,
                    'cantidad' => $cantidad,
                    'subtotal' => $subtotal,
                ];
                $total += $subtotal;
            }
        }

        return $this->render('carrito/index.html.twig', [
            'items' => $items,
            'total' => $total,
        ]);
    }

    #[Route('/add/{id}', name: 'app_carrito_add')]
    public function add(int $id, Request $request, SessionInterface $session, ProductoRepository $productoRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $producto = $productoRepository->find($id);
        if (!$producto) {
            throw $this->createNotFoundException('Producto no encontrado');
        }

        $carrito = $session->get('carrito', []);

        if (isset($carrito[$id])) {
            $carrito[$id]++;
        } else {
            $carrito[$id] = 1;
        }

        $session->set('carrito', $carrito);

        $this->addFlash('success', 'Producto añadido al carrito');

        $referer = $request->headers->get('referer');
        return $this->redirect($referer ?? $this->generateUrl('app_home'));
    }

    #[Route('/remove/{id}', name: 'app_carrito_remove')]
    public function remove(int $id, SessionInterface $session): Response
    {
        $carrito = $session->get('carrito', []);

        if (isset($carrito[$id])) {
            unset($carrito[$id]);
            $session->set('carrito', $carrito);
        }

        return $this->redirectToRoute('app_carrito');
    }

    #[Route('/clear', name: 'app_carrito_clear')]
    public function clear(SessionInterface $session): Response
    {
        $session->remove('carrito');
        return $this->redirectToRoute('app_carrito');
    }
}
