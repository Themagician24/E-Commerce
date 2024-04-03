<?php

namespace App\Form;

use App\Entity\Adresse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdresseUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname',  TextType::class , [
               'label' => ' Prenom',
               'attr' => [
                    'placeholder' => 'Ajouter votre prenom ...'
               ]
            ])
            ->add('lastname' ,  TextType::class , [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Ajouter votre nom ...'
                ]
            ])
            ->add('adresse' ,  TextType::class , [
                'label' => ' Adresse',
                'attr' => [
                    'placeholder' => 'Indiquez votre adresse'
                ]
            ])
            ->add('postal',  TextType::class , [
                'label' => ' Votre code postal',
                'attr' => [
                    'placeholder' => 'Indiquez votre code postal'
                ]
            ])
            ->add('city' ,  TextType::class , [
                'label' => 'Ville',
                'attr' => [
                    'placeholder' => 'Ajouter votre Ville'
                ]
            ])
            ->add('country' ,  CountryType::class , [
                'label' => ' Votre Pays',

            ])
            ->add('phone' ,  TextType::class , [
                'label' => ' Téléphone',
                'attr' => [
                    'placeholder' => 'Ajouter votre numéro de téléphone'
                ]
            ])
            ->add('submit',SubmitType::class,[
                'label' => 'Sauvegarder',
                'attr' => [
                    'class' => 'btn btn-danger',
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Adresse::class,
        ]);
    }
}
