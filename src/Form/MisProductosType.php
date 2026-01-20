<?php

namespace App\Form;

use App\Entity\Categoria;
use App\Entity\Producto;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class MisProductosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titulo', TextType::class, [
                'label' => 'Título del producto',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Nombre del producto'],
                'required' => false,
            ])
            ->add('descripcion', TextareaType::class, [
                'required' => false,
                'label' => 'Descripción',
                'attr' => ['class' => 'form-control', 'rows' => 4, 'placeholder' => 'Describe tu producto...'],
            ])
            ->add('precio', MoneyType::class, [
                'currency' => 'EUR',
                'label' => 'Precio',
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('categoria', EntityType::class, [
                'class' => Categoria::class,
                'choice_label' => 'nombre',
                'label' => 'Categoría',
                'attr' => ['class' => 'form-select'],
                'placeholder' => 'Selecciona una categoría',
                'required' => false,
            ])
            ->add('fechaInicial', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Fecha inicio de venta',
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('fechaFinal', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Fecha fin de venta',
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('imagenFile', FileType::class, [
                'label' => 'Imagen del producto',
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Por favor sube una imagen válida (JPEG, PNG o GIF)',
                    ])
                ],
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
