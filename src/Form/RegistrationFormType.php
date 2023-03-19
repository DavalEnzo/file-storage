<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\Validator\Constraints as Assert;

class RegistrationFormType extends AbstractType
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

            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'Les mots de passe doivent être identiques', // message d'erreur si les mots de passe ne sont pas identiques
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true, // le mot de passe est obligatoire
                'first_options'  => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Votre mot de passe',
                        'required' => true,
                        'minlength' => 2,
                        'maxlength' => 50,
                        // carateres speciaux majuscules minuscules chiffres obligatoires
                        'pattern' => '^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{8,}$',
                        'title' => 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial'
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmez votre mot de passe',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Confirmez votre mot de passe',
                        'required' => true,
                        'minlength' => 2,
                        'maxlength' => 50
                    ]
                ], 
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'min' => 6,
                        'max' => 255,
                    ]),
                ],
            ])  
            
        // ajoute un champ de type checkbox pour accepter achat de produits
        ->add('is_buyer', CheckboxType::class, [
            'mapped' => false,
            'label' => "Vous devez accepter et valider l'achat de stockage",
            'constraints' => [
                new Assert\IsTrue([
                    'message' => 'Vous devez acheter du stockage',
                ]),
            ],
        ])
            
                                 
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
