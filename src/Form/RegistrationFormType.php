<?php

namespace App\Form;

use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombreUsuario', TextType::class, [
                'label' => 'Nombre de usuario',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'Debes introducir un nombre de usuario']),
                    new Length([
                        'min' => 3,
                        'max' => 50,
                        'minMessage' => 'El nombre de usuario debe tener al menos {{ limit }} caracteres',
                        'maxMessage' => 'El nombre de usuario no puede tener más de {{ limit }} caracteres',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Correo electrónico',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'Debes introducir un correo electrónico']),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password', 'class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'Debes introducir una contraseña']),
                ],
                'label' => 'Contraseña',
            ])
            ->add('captcha', TextType::class, [
                'mapped' => false,
                'label' => 'Código de verificación',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'Introduce el código de verificación']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
        ]);
    }
}