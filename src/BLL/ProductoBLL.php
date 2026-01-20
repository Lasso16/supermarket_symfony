<?php

namespace App\BLL;

use App\Entity\Producto;
use App\Repository\ProductoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ProductoBLL extends BaseBLL
{
    private ProductoRepository $productoRepository;
    private \App\Repository\CompraProductoRepository $compraProductoRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        \Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface $passwordHasher,
        TokenStorageInterface $tokenStorage,
        ProductoRepository $productoRepository,
        \App\Repository\CompraProductoRepository $compraProductoRepository,
        string $imagesDirectoryGallery,
        string $imagesDirectoryPortfolio
    ) {
        parent::__construct($entityManager, $validator, $passwordHasher, $tokenStorage, $imagesDirectoryGallery, $imagesDirectoryPortfolio);
        $this->productoRepository = $productoRepository;
        $this->compraProductoRepository = $compraProductoRepository;
    }

    // ... (toArray, getAll, checkAccessToProducto, getByUser, getById remain same)

    public function toArray(Producto $producto): array
    {
        return [
            'id' => $producto->getId(),
            'titulo' => $producto->getTitulo(),
            'descripcion' => $producto->getDescripcion(),
            'precio' => $producto->getPrecio(),
            'imagen' => $producto->getImagen(),
            'categoria' => $producto->getCategoria() ? [
                'id' => $producto->getCategoria()->getId(),
                'nombre' => $producto->getCategoria()->getNombre()
            ] : null,
            'usuario' => [
                'id' => $producto->getUsuario()->getId(),
                'nombre' => $producto->getUsuario()->getNombreUsuario()
            ],
            'fecha_creacion' => $producto->getFechaCreacion()?->format('Y-m-d H:i:s'),
            'fecha_inicial' => $producto->getFechaInicial()?->format('Y-m-d'),
            'fecha_final' => $producto->getFechaFinal()?->format('Y-m-d'),
        ];
    }

    public function getAll(array $filters = [], bool $enforceSecurity = false): array
    {
        if ($enforceSecurity) {
            $user = $this->getUser();
            if (!$user instanceof \App\Entity\Usuario) {
                return [];
            }
            if (!$this->checkRoleAdmin()) {
                $filters['usuario'] = $user;
            }
            return $this->productoRepository->findAllFiltered($filters);
        }

        if (empty($filters)) {
            return $this->productoRepository->findAll();
        }
        return $this->productoRepository->findAllFiltered($filters);
    }

    public function checkAccessToProducto(Producto $producto): void
    {
        if ($this->checkRoleAdmin() === false) {
            $usuario = $this->getUser();
            if (!$usuario || $usuario->getId() !== $producto->getUsuario()->getId()) {
                throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Acceso denegado.');
            }
        }
    }

    public function getByUser(UserInterface $user): array
    {
        return $this->productoRepository->findByUsuario($user->getId());
    }

    public function getById(int $id): ?Producto
    {
        return $this->productoRepository->find($id);
    }

    public function create(Producto $producto, array $data = [], bool $flush = true): void
    {
        // Asignar usuario logueado
        $usuario = $this->getUser();
        if ($usuario instanceof \App\Entity\Usuario) {
            $producto->setUsuario($usuario);
        }

        // Procesar imagen si viene en data (API)
        if (!empty($data['imagen'])) {
            $filename = $this->getImagenGaleria($data['imagen']);
            if ($filename) {
                $producto->setImagen($filename);
            }
        }

        $this->entityManager->persist($producto);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function update(Producto $producto, array $data = [], bool $flush = true): void
    {
        // Procesar imagen si viene en data (API)
        if (!empty($data['imagen'])) {
            $filename = $this->getImagenGaleria($data['imagen']);
            if ($filename) {
                $producto->setImagen($filename);
            }
        }

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    private function getImagenGaleria(string $imagen): ?string
    {
        if (empty($imagen)) {
            return null;
        }

        // Check if it's already a filename (simple check)
        if (strpos($imagen, 'data:') === false && strpos($imagen, '.') !== false) {
            return $imagen;
        }

        // It is a base64 string
        $data = explode(',', $imagen);
        if (count($data) < 2) {
            return null; // Invalid format
        }

        $format = str_replace(
            ['data:image/', ';', 'base64'],
            ['', '', ''],
            $data[0]
        );

        $imageData = base64_decode($data[1]);
        $imageName = uniqid() . '.' . $format;

        // Use the gallery directory injected in BaseBLL
        $path = $this->imagesDirectoryGallery . '/' . $imageName;

        try {
            file_put_contents($path, $imageData);
            return $imageName;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function delete(Producto $producto, UserInterface $user, bool $flush = true): void
    {
        $isOwner = $producto->getUsuario() === $user;
        $isAdmin = in_array('ROLE_ADMIN', $user->getRoles());

        if (!$isOwner && !$isAdmin) {
            throw new AccessDeniedException('No tienes permiso para eliminar este producto.');
        }

        $compras = $this->compraProductoRepository->findBy(['producto' => $producto]);
        if (count($compras) > 0) {
            throw new \Exception('No se puede eliminar este producto porque ya ha sido comprado por ' . count($compras) . ' usuario(s).');
        }

        $this->productoRepository->remove($producto, $flush);
    }
}
