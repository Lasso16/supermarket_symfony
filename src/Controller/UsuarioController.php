<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\UsuarioType;
use App\Repository\CompraRepository;
use App\Repository\ProductoRepository;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/usuarios')]
#[IsGranted('ROLE_ADMIN')]
class UsuarioController extends AbstractController
{
    #[Route('/', name: 'app_usuario_index', methods: ['GET'])]
    public function index(Request $request, UsuarioRepository $usuarioRepository): Response
    {
        $tipoFilter = $request->query->get('tipo');

        if ($tipoFilter) {
            $usuarios = $usuarioRepository->findBy(['tipo' => $tipoFilter]);
        } else {
            $usuarios = $usuarioRepository->findAll();
        }

        return $this->render('usuario/index.html.twig', [
            'usuarios' => $usuarios,
            'tipoFilter' => $tipoFilter,
        ]);
    }

    #[Route('/cambiar-rol/{id}', name: 'app_usuario_cambiar_rol', methods: ['POST'])]
    public function cambiarRol(int $id, Request $request, UsuarioRepository $usuarioRepository, EntityManagerInterface $entityManager): Response
    {
        $usuario = $usuarioRepository->find($id);
        if (!$usuario) {
            throw $this->createNotFoundException('Usuario no encontrado');
        }

        if (!$this->isCsrfTokenValid('cambiar-rol' . $usuario->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token inválido');
            return $this->redirectToRoute('app_usuario_index');
        }

        $nuevoRol = $request->request->get('nuevo_rol');

        // Update roles array
        if ($nuevoRol === 'ROLE_ADMIN') {
            $usuario->setRoles(['ROLE_ADMIN']);
            $usuario->setTipo('admin'); // Keep tipo in sync
        } else {
            $usuario->setRoles(['ROLE_USER']); // Explicitly set ROLE_USER as requested
            $usuario->setTipo('normal');
        }

        $entityManager->flush();
        $this->addFlash('success', 'Rol de usuario actualizado correctamente.');

        return $this->redirectToRoute('app_usuario_index');
    }

    #[Route('/delete/{id}', name: 'app_usuario_delete', methods: ['POST'])]
    public function delete(int $id, Request $request, UsuarioRepository $usuarioRepository, ProductoRepository $productoRepository, CompraRepository $compraRepository, EntityManagerInterface $entityManager): Response
    {
        $usuario = $usuarioRepository->find($id);
        if (!$usuario) {
            throw $this->createNotFoundException('Usuario no encontrado');
        }

        if (!$this->isCsrfTokenValid('delete' . $usuario->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token inválido');
            return $this->redirectToRoute('app_usuario_index');
        }

        // Cannot delete yourself
        if ($usuario === $this->getUser()) {
            $this->addFlash('error', 'No puedes eliminarte a ti mismo.');
            return $this->redirectToRoute('app_usuario_index');
        }

        // Check if user has created products
        $productos = $productoRepository->findBy(['usuario' => $usuario]);
        if (count($productos) > 0) {
            $this->addFlash('error', 'No se puede eliminar este usuario porque tiene ' . count($productos) . ' producto(s) publicado(s).');
            return $this->redirectToRoute('app_usuario_index');
        }

        // Check if user has made purchases
        $compras = $compraRepository->findByUsuario($usuario->getId());
        if (count($compras) > 0) {
            $this->addFlash('error', 'No se puede eliminar este usuario porque tiene ' . count($compras) . ' compra(s) realizada(s).');
            return $this->redirectToRoute('app_usuario_index');
        }

        $entityManager->remove($usuario);
        $entityManager->flush();

        $this->addFlash('success', 'Usuario eliminado correctamente.');
        return $this->redirectToRoute('app_usuario_index');
    }
}
