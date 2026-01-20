<?php

namespace App\BLL;

use App\Entity\Usuario;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class UsuarioBLL extends BaseBLL
{
    private UsuarioRepository $usuarioRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher,
        TokenStorageInterface $tokenStorage,
        UsuarioRepository $usuarioRepository,
        string $imagesDirectoryGallery,
        string $imagesDirectoryPortfolio
    ) {
        parent::__construct($entityManager, $validator, $passwordHasher, $tokenStorage, $imagesDirectoryGallery, $imagesDirectoryPortfolio);
        $this->usuarioRepository = $usuarioRepository;
    }

    public function nuevo(string $username, string $email, string $password): JsonResponse
    {
        $usuario = new Usuario();
        $usuario->setNombreUsuario($username);
        $usuario->setEmail($email);
        $usuario->setRoles(['ROLE_USER']);

        // Hash password
        $hashedPassword = $this->encoder->hashPassword(
            $usuario,
            $password
        );
        $usuario->setPassword($hashedPassword);

        return $this->guardaValidando($usuario);
    }

    public function toArray(Usuario $usuario): array
    {
        return [
            'id' => $usuario->getId(),
            'username' => $usuario->getNombreUsuario(),
            'email' => $usuario->getEmail(),
            'roles' => $usuario->getRoles(),
        ];
    }

    public function getAll(): array
    {
        return $this->usuarioRepository->findAll();
    }

    public function profile(): array
    {
        /** @var Usuario|null $usuario */
        $usuario = $this->getUser();
        if (!$usuario) {
            return []; // O lanzar excepción si se requiere autenticación estricta
        }
        return $this->toArray($usuario);
    }

    public function cambiaPassword(string $nuevoPassword): JsonResponse
    {
        /** @var Usuario|null $usuario */
        $usuario = $this->getUser();
        if (!$usuario) {
            return new JsonResponse(['error' => 'Usuario no autenticado'], 401);
        }

        $usuario->setPassword($this->encoder->hashPassword($usuario, $nuevoPassword));

        return $this->guardaValidando($usuario);
    }
}
