<?php

namespace App\Controller;

use App\Form\ChangeAvatarType;
use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/perfil')]
class PerfilController extends AbstractController
{
    #[Route('/', name: 'app_perfil')]
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var \App\Entity\Usuario $usuario */
        $usuario = $this->getUser();

        // Password form
        $passwordForm = $this->createForm(ChangePasswordType::class);
        $passwordForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $currentPassword = $passwordForm->get('currentPassword')->getData();
            $newPassword = $passwordForm->get('newPassword')->getData();

            // Verify current password
            if (!$passwordHasher->isPasswordValid($usuario, $currentPassword)) {
                $this->addFlash('error', 'La contraseña actual no es correcta.');
            } else {
                $hashedPassword = $passwordHasher->hashPassword($usuario, $newPassword);
                $usuario->setPassword($hashedPassword);
                $entityManager->flush();
                $this->addFlash('success', 'Contraseña actualizada correctamente.');
            }

            return $this->redirectToRoute('app_perfil');
        }

        // Avatar form
        $avatarForm = $this->createForm(ChangeAvatarType::class);
        $avatarForm->handleRequest($request);

        if ($avatarForm->isSubmitted() && $avatarForm->isValid()) {
            $avatarFile = $avatarForm->get('avatar')->getData();

            if ($avatarFile) {
                // Check file size (10KB = 10240 bytes)
                if ($avatarFile->getSize() > 10240) {
                    $this->addFlash('error', 'La imagen no puede superar los 10KB.');
                    return $this->redirectToRoute('app_perfil');
                }

                $newFilename = 'avatar_' . $usuario->getId() . '_' . uniqid() . '.' . $avatarFile->guessExtension();
                $avatarsDir = $this->getParameter('kernel.project_dir') . '/public/images/avatars';

                // Create directory if not exists
                if (!is_dir($avatarsDir)) {
                    mkdir($avatarsDir, 0755, true);
                }

                try {
                    // Resize image to 100x100
                    $this->resizeImage($avatarFile->getPathname(), $avatarsDir . '/' . $newFilename, 100, 100);

                    $usuario->setAvatar('avatars/' . $newFilename);
                    $entityManager->flush();
                    $this->addFlash('success', 'Avatar actualizado correctamente.');
                } catch (FileException $e) {
                    $this->addFlash('error', 'Error al subir la imagen.');
                }
            }

            return $this->redirectToRoute('app_perfil');
        }

        return $this->render('perfil/index.html.twig', [
            'usuario' => $usuario,
            'passwordForm' => $passwordForm->createView(),
            'avatarForm' => $avatarForm->createView(),
        ]);
    }

    private function resizeImage(string $sourcePath, string $destPath, int $width, int $height): void
    {
        $imageInfo = getimagesize($sourcePath);
        $mimeType = $imageInfo['mime'];

        switch ($mimeType) {
            case 'image/jpeg':
                $sourceImage = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $sourceImage = imagecreatefrompng($sourcePath);
                break;
            default:
                throw new \Exception('Tipo de imagen no soportado');
        }

        $destImage = imagecreatetruecolor($width, $height);

        // Handle PNG transparency
        if ($mimeType === 'image/png') {
            imagealphablending($destImage, false);
            imagesavealpha($destImage, true);
        }

        imagecopyresampled(
            $destImage,
            $sourceImage,
            0,
            0,
            0,
            0,
            $width,
            $height,
            imagesx($sourceImage),
            imagesy($sourceImage)
        );

        // Save based on extension
        $ext = pathinfo($destPath, PATHINFO_EXTENSION);
        if ($ext === 'png') {
            imagepng($destImage, $destPath);
        } else {
            imagejpeg($destImage, $destPath, 90);
        }

        imagedestroy($sourceImage);
        imagedestroy($destImage);
    }
}
