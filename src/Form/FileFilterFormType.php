<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FileFilterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
                'required' => false,
            ])
            ->add('format', TextType::class, [
                'label' => 'Format',
                'required' => false,
            ])
            ->add('size_min', IntegerType::class, [
                'attr' => [
                    'max' => '20000',
                ],
                'label' => 'Size',
                'required' => false,
            ])
            ->add('size_max', IntegerType::class, [
                'attr' => [
                    'max' => '20000',
                ],
                'label' => 'Size',
                'required' => false,
            ])
            ->add('date_min', DateType::class, [
                'label' => 'Begin',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('date_max', DateType::class, [
                'label' => 'End',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('order_size', ChoiceType::class, [
                'choices' => [
                    '' => null,
                    'Croissant' => 'ASC',
                    'Décroissant' => 'DESC',
                ]
            ])
            ->add('order_date', ChoiceType::class, [
                'choices' => [
                    '' => null,
                    'Croissant' => 'ASC',
                    'Décroissant' => 'DESC',
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Filtrer',
            ]);
    }
}
