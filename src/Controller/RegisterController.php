<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response //L'injection de dependance ce qui veux dire que ma fonction va dependre d'une autre fonction 
    {
        $user = new User();
        $form = $this ->createForm(RegisterUserType::class,$user);//Instancier notre formulaire dans une variable $form

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) { //Si le formulaire est soumis alors:

            $entityManager->persist($user);   // Persist c'est pour figer les donnees 
            $entityManager->flush();    // Pour enregister les donnees

            $this->addFlash(
                'success',
                "Votre compte est corectement créer!!Veuillez vous connecter."
            );
//ENvoies d'un mail de confirmation d'inscription
            $mail = new Mail();
            $vars = [
                'firstname' => $user->getFirstname()
            ];
            $mail->send($user->getEmail(), $user->getFirstname(). ' '.$user->getLastname(),
                'Bienvenue sur notre Boutique Africaine', "welcome.html", $vars);

            return $this->redirectToRoute('app_login');
        }

 //Tu enregistre les datas en BDD
//Tu envoies une notification de succes du compte bien creer


        return $this->render('register/index.html.twig',[
            'registerForm' => $form->createView()
        ]);

    }
}
