<?php

namespace App\Form;

use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombreUsuario', TextType::class, ['label' => 'Nombre de Usuario'])
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class)
            ->add('tipo', ChoiceType::class, [
                'choices' => [
                    'Normal' => 'normal',
                    'Admin' => 'admin',
                ],
            ])
            ->add('avatar', TextType::class, [
                'required' => false,
                'label' => 'URL de Avatar (opcional)'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
        ]);
    }
}
