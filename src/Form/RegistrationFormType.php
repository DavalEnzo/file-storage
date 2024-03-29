<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Validator\Constraints as Assert;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('firstname' , TextType::class, [
            'label' => 'Prénom',
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
            'label' => 'Adresse',
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'Votre adresse',
                'required' => true,
                'minlength' => 2,
                'maxlength' => 50
            ]
            ])
        ->add('postal_code', TextType::class, [
            'label' => 'Code postal',
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'Votre code postal',
                'required' => true,
                'minlength' => 2,
                'maxlength' => 50
            ]
        ])
        ->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'mapped' => false,
            'invalid_message' => 'Les mots de passe doivent être identiques',
            'options' => ['attr' => ['class' => 'password-field']],
            'required' => true,
            'first_options'  => [
                'label' => 'Mot de passe',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre mot de passe',
                    'required' => true,
                    'minlength' => 2,
                    'maxlength' => 50,
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
        ->add('submit' , SubmitType::class, [
            'label' => 'S\'inscrire',
            'attr' => [
                'class' => 'btn btn-primary'
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
