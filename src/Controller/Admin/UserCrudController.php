<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs')


            ;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
           TextField::new('firstname')->setLabel('Prénom'),
            TextField::new('lastname')->setLabel('Nom de famille'),
            DateField::new('lastLoginAt')->setLabel('Derniere connection')->onlyOnIndex(),
            ChoiceField::new('roles')->setLabel('Permissions')->setHelp('Vous pouvez choicir le role de cet utilisateur')->setChoices([
                'ROLE_USER' => 'ROLE_USER',
                'ROLE_ADMIN' => 'ROLE_ADMIN',

            ])->allowMultipleChoices(),
            TextField::new('email')->setLabel('Email')->onlyOnIndex(),


        ];
    }

}