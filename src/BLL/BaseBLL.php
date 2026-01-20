<?php

namespace App\BLL;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

abstract class BaseBLL
{
    protected EntityManagerInterface $entityManager;
    protected ValidatorInterface $validator;
    protected UserPasswordHasherInterface $encoder;
    protected TokenStorageInterface $tokenStorage;
    protected string $imagesDirectoryGallery;
    protected string $imagesDirectoryPortfolio;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher,
        TokenStorageInterface $tokenStorage,
        string $imagesDirectoryGallery,
        string $imagesDirectoryPortfolio
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->encoder = $passwordHasher;
        $this->tokenStorage = $tokenStorage;
        $this->imagesDirectoryGallery = $imagesDirectoryGallery;
        $this->imagesDirectoryPortfolio = $imagesDirectoryPortfolio;
    }

    public function getUser(): ?object
    {
        return $this->tokenStorage->getToken()?->getUser();
    }

    protected function checkRoleAdmin(): bool
    {
        $usuario = $this->getUser();
        if ($usuario && method_exists($usuario, 'hasRole') && $usuario->hasRole('ROLE_ADMIN') === true) {
            return true;
        }
        return false;
    }



    public function validate(object $entity): array
    {
        $errors = $this->validator->validate($entity);
        $errorMessages = [];

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
            }
        }

        return $errorMessages;
    }

    public function guardaValidando(object $entity): JsonResponse
    {
        $errors = $this->validate($entity);

        if (count($errors) > 0) {
            return new JsonResponse([
                'status' => 'error',
                'code' => Response::HTTP_BAD_REQUEST,
                'message' => 'Errores de validación',
                'errors' => $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            return new JsonResponse([
                'status' => 'success',
                'code' => Response::HTTP_CREATED,
                'data' => method_exists($this, 'toArray') ? $this->toArray($entity) : ['id' => $entity->getId()]
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
