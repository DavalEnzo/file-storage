<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class InscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname' , TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre prénom',
                    'required' => true,
                    'minlength' => 2,
                    'maxlength' => 50
                ]
            ])
            ->add('lastname', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre nom',
                    'required' => true,
                    'minlength' => 2,
                    'maxlength' => 50
                ]
                ])
                ->add('email', TextType::class, [
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Votre email',
                    ],
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\Email(message : "L'email n'est pas valide"),
                        new Assert\Length([
                            'min' => 2,
                            'max' => 255,
                        ]),
                    ],
                ])          
            ->add('password', PasswordType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre mot de passe',
                    'required' => true,
                ]
                ])
            ->add('address', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre adresse',
                    'required' => true,
                    'minlength' => 2,
                    'maxlength' => 50
                ]
                ])
            ->add('postal_code', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre code postal',
                    'required' => true,
                    'minlength' => 2,
                    'maxlength' => 50
                ]
                ])
                ->add('create_datetime', DateType::class, [
                        'widget' => 'single_text',
                        'label' => false,
                        'format' => 'yyyy-MM-dd',
                        'disabled' => true,
                        'attr' => [
                            'class' => 'form-control',
                            'required' => true,
                            'hidden' => true, // cache le champ de la date
                        ]
                    ])




            // ->add('storage',//ajouter une case a choser obligatoire
            //     ChoiceType::class,
            //     [
            //         'choices' => [
            //             'Oui' => true,
            //             'Non' => false,
            //         ],
            //         'expanded' => true,
            //         'multiple' => false,
            //     ]
                
            // )
            ->add('submit' , SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                    
                ]
            ])
            
        ;
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
