<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\RegistrationFormType;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, UserAuthenticatorInterface $userAuthenticator, FormLoginAuthenticator $formLoginAuthenticator): Response
    {
        $user = new Usuario();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Verify Captcha
            $captchaCode = $request->getSession()->get('captcha_code');
            $submittedCaptcha = $form->get('captcha')->getData();

            if (!$captchaCode || $captchaCode != $submittedCaptcha) {
                $this->addFlash('verify_email_error', 'El código de verificación es incorrecto.');
                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $form->createView(),
                ]);
            }

            // Clean captcha from session
            $request->getSession()->remove('captcha_code');

            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // Assign default role
            $user->setRoles(['ROLE_USER']);

            $entityManager->persist($user);
            $entityManager->flush();

            // Inject credentials for FormLoginAuthenticator to avoid "The key "_username" must be a string" error
            $request->request->set('_username', $form->get('nombreUsuario')->getData());
            $request->request->set('_password', $form->get('plainPassword')->getData());

            // Auto-login
            return $userAuthenticator->authenticateUser(
                $user,
                $formLoginAuthenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
