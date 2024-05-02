<?php

namespace App\Controller\Admin;

use App\Entity\Header;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class HeaderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Header::class;
    }


    public function configureFields(string $pageName): iterable
    {
        $required = true;
        if ($pageName == 'edit') {
            $required = false;
        }



        return [

            TextField::new('title', 'Titre'),
            TextField::new('content', 'Contenu'),
            TextField::new('buttonTitle', 'Titre du bouton'),
            TextField::new('buttonLink', 'URL du bouton'),
            ImageField::new('illustration')
                ->setLabel('Image du fond du header')
                ->setHelp('Image de fond du header en JPEG ou PNG')
                ->setUploadedFileNamePattern('[year]-[month]-[day]-[contenthash].[extension]')
                ->setUploadDir('/public/uploads')
                ->setRequired($required)
                ->setBasePath('/uploads'),




        ];
    }

}
