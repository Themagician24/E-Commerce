<?php

namespace App\Form;

// Importation des classes nécessaires pour la création du formulaire
use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class RegisterUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Construction du formulaire
        $builder
            // Ajout du champ email avec des options spécifiques
            ->add('email', EmailType::class, [
                'label' => ' Email ',
                'attr' => [
                    'placeholder' => "Indiquez votre adresse email"
                ]
            ])
        
            // Ajout du champ mot de passe avec des contraintes de longueur et des options spécifiques
            ->add('plainPassword', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'constraints' => [
                        new Length([
                            'min' => 4,
                            'max' => 30
                        ])
                    ],
                    'first_options'  =>
                     [
                        'label' => 'Votre mots de passe', 
                        'attr' => [
                            'placeholder' => "Choissessez votre mots de passe"
                        ],
                     'hash_property_path' => 'password'
                    ],
                    'second_options' => [
                        'label' => 'Confirmez votre mots de passe',
                        'attr' => [
                            'placeholder' => "Confirmez votre mots de passe"
                        ]
                    ],
                    'mapped' => false,
                ])

            
            // Ajout du champ prénom avec des contraintes de longueur et des options spécifiques
            ->add('firstname', TextType::class, [
                'label' => "Prenom",
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'max' => 30
                    ])
                    ],
                'attr' => [
                    'placeholder' => "Indiquez votre Prenom"
                ],
            ])
            // Ajout du champ nom avec des contraintes de longueur et des options spécifiques
            ->add('lastname', TextType::class, [
                'label' => "Nom",
                'constraints' => [
                    new Length([
                        'min' => 1,
                        'max' => 30
                    ])
                    ],
                'attr' => [
                    'placeholder' => "Indiquez votre Nom"
                ]
            ])
            // Ajout du bouton de soumission avec des options spécifiques
            ->add('submit', SubmitType::class,[
                'label' => 'Validez',
                'attr' => [
                    'class' => 'btn btn-success',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // Configuration des options par défaut pour le formulaire
        $resolver->setDefaults([
            'contraints' => [
                new UniqueEntity([
                    'entityClass' => User::class,
                    'fields' => 'email'
                ])
                ],
            'data_class' => User::class,
        ]);
    }
}