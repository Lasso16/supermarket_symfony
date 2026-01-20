<?php

namespace App\Controller;

use App\Entity\Compra;
use App\Entity\CompraProducto;
use App\Repository\CompraRepository;
use App\Repository\ProductoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/compra')]
class CompraController extends AbstractController
{
    #[Route('/checkout', name: 'app_compra_checkout')]
    public function checkout(
        SessionInterface $session,
        ProductoRepository $productoRepository,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $carrito = $session->get('carrito', []);

        if (empty($carrito)) {
            $this->addFlash('error', 'El carrito está vacío');
            return $this->redirectToRoute('app_carrito');
        }

        /** @var \App\Entity\Usuario $usuario */
        $usuario = $this->getUser();

        $compra = new Compra();
        $compra->setUsuario($usuario);
        $total = 0;

        $vendedoresNotificados = [];

        foreach ($carrito as $id => $cantidad) {
            $producto = $productoRepository->find($id);
            if ($producto) {
                $item = new CompraProducto();
                $item->setProducto($producto);
                $item->setCantidad($cantidad);
                $item->setPrecioUnitario($producto->getPrecio());
                $compra->addItem($item);

                $subtotal = (float)$producto->getPrecio() * $cantidad;
                $total += $subtotal;

                // Collect seller info for email notification
                $vendedor = $producto->getUsuario();
                if ($vendedor && $vendedor->getEmail() && !in_array($vendedor->getId(), $vendedoresNotificados)) {
                    $vendedoresNotificados[] = $vendedor->getId();

                    // Send email to seller
                    try {
                        $email = (new Email())
                            ->from('noreply@organic-shop.com')
                            ->to($vendedor->getEmail())
                            ->subject('¡Nueva compra de tu producto!')
                            ->html(sprintf(
                                '<p>El usuario <strong>%s</strong> ha comprado tu producto <strong>%s</strong> (x%d).</p>
                                <p>Precio: %.2f €</p>
                                <p>¡Gracias por vender en Organic Shop!</p>',
                                $usuario->getNombreUsuario(),
                                $producto->getTitulo(),
                                $cantidad,
                                $subtotal
                            ));
                        $mailer->send($email);
                    } catch (\Exception $e) {
                        // Log error but don't stop the purchase
                    }
                }
            }
        }

        $compra->setTotal((string)$total);
        $entityManager->persist($compra);
        $entityManager->flush();

        // Clear cart
        $session->remove('carrito');

        return $this->render('compra/confirmation.html.twig', [
            'compra' => $compra,
        ]);
    }

    #[Route('/historial', name: 'app_compra_historial')]
    public function historial(CompraRepository $compraRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var \App\Entity\Usuario $usuario */
        $usuario = $this->getUser();

        $compras = $compraRepository->findByUsuario($usuario->getId());

        return $this->render('compra/historial.html.twig', [
            'compras' => $compras,
        ]);
    }
}
