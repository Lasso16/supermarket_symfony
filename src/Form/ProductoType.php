<?php

namespace App\Form;

use App\Entity\Categoria;
use App\Entity\Producto;
use App\Entity\Usuario;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titulo', TextType::class)
            ->add('descripcion', TextareaType::class, ['required' => false])
            ->add('precio', MoneyType::class, [
                'currency' => 'EUR',
            ])
            ->add('imagenFile', \Symfony\Component\Form\Extension\Core\Type\FileType::class, [
                'label' => 'Imagen (JPG, PNG)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Por favor sube una imagen válida (JPG o PNG)',
                    ])
                ],
            ])
            ->add('usuario', EntityType::class, [
                'class' => Usuario::class,
                'choice_label' => 'nombreUsuario',
                'label' => 'Vendedor'
            ])
            ->add('categoria', EntityType::class, [
                'class' => Categoria::class,
                'choice_label' => 'nombre',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Producto::class,
        ]);
    }
}
