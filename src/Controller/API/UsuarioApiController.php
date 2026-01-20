<?php

namespace App\Controller\API;

use App\BLL\UsuarioBLL;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1')]
class UsuarioApiController extends BaseApiController
{
    #[Route('/auth/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request, UsuarioBLL $userBLL): Response
    {
        $data = $this->getContent($request);

        $username = $data['username'] ?? null;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$username || !$email || !$password) {
            return $this->getResponse(['error' => 'Faltan datos obligatorios (username, email, password)'], Response::HTTP_BAD_REQUEST);
        }

        // Delegamos en BLL que ya maneja validación y respuesta JSON estandarizada
        return $userBLL->nuevo($username, $email, $password);
    }

    #[Route('/profile', name: 'api_profile', methods: ['GET'])]
    public function profile(UsuarioBLL $usuarioBLL): Response
    {
        $usuario = $usuarioBLL->profile();
        if (empty($usuario)) {
            return $this->getResponse(['error' => 'Usuario no autenticado'], Response::HTTP_UNAUTHORIZED);
        }
        return $this->getResponse($usuario);
    }

    #[Route('/profile/password', name: 'api_change_password', methods: ['PATCH'])]
    public function cambiaPassword(Request $request, UsuarioBLL $usuarioBLL): Response
    {
        $data = $this->getContent($request);

        if (empty($data['password'])) {
            return $this->getResponse(['error' => 'No se ha recibido el password'], Response::HTTP_BAD_REQUEST);
        }

        return $usuarioBLL->cambiaPassword($data['password']);
    }
}
