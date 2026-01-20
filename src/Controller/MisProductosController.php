<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Form\MisProductosType;
use App\Repository\CompraProductoRepository;
use App\Repository\ProductoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/mis-productos')]
class MisProductosController extends AbstractController
{
    #[Route('/', name: 'app_mis_productos')]
    public function index(\App\BLL\ProductoBLL $productoBLL): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var \App\Entity\Usuario $usuario */
        $usuario = $this->getUser();

        $productos = $productoBLL->getByUser($usuario);

        return $this->render('mis_productos/index.html.twig', [
            'productos' => $productos,
        ]);
    }

    #[Route('/new', name: 'app_mis_productos_new')]
    public function new(Request $request, \App\BLL\ProductoBLL $productoBLL, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $producto = new Producto();
        $form = $this->createForm(MisProductosType::class, $producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \App\Entity\Usuario $usuario */
            $usuario = $this->getUser();
            $producto->setUsuario($usuario);

            // Handle image upload
            // Handle image upload
            $imageFile = $form->get('imagenFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/images/gallery',
                        $newFilename
                    );
                    $producto->setImagen($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Error al subir la imagen');
                }
            }

            $productoBLL->create($producto);

            $this->addFlash('success', 'Producto creado correctamente');
            return $this->redirectToRoute('app_mis_productos');
        }

        return $this->render('mis_productos/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'app_mis_productos_edit')]
    public function edit(int $id, Request $request, \App\BLL\ProductoBLL $productoBLL, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $producto = $productoBLL->getById($id);
        if (!$producto) {
            throw $this->createNotFoundException('Producto no encontrado');
        }

        // Check ownership
        if ($producto->getUsuario() !== $this->getUser()) {
            throw $this->createAccessDeniedException('No puedes editar este producto');
        }

        $form = $this->createForm(MisProductosType::class, $producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload
            // Handle image upload
            $imageFile = $form->get('imagenFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/images/gallery',
                        $newFilename
                    );
                    $producto->setImagen($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Error al subir la imagen');
                }
            }

            $productoBLL->update($producto);

            $this->addFlash('success', 'Producto actualizado correctamente');
            return $this->redirectToRoute('app_mis_productos');
        }

        return $this->render('mis_productos/edit.html.twig', [
            'producto' => $producto,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'app_mis_productos_delete', methods: ['POST'])]
    public function delete(int $id, Request $request, \App\BLL\ProductoBLL $productoBLL): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $producto = $productoBLL->getById($id);
        if (!$producto) {
            throw $this->createNotFoundException('Producto no encontrado');
        }

        // Check CSRF
        if (!$this->isCsrfTokenValid('delete' . $producto->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token inválido');
            return $this->redirectToRoute('app_mis_productos');
        }

        try {
            $productoBLL->delete($producto, $this->getUser());
            $this->addFlash('success', 'Producto eliminado correctamente');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('app_mis_productos');
    }
}
