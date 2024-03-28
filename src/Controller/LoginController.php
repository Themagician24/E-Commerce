<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/connexion', name: 'app_login')]
    public function index(AuthenticationUtils $autheticationUtils): Response //Injections de dependances ()
    {
        //Gerer les erreurs
        $error = $autheticationUtils->getLastAuthenticationError();

        //Dernier username (email)
        $lastUsername = $autheticationUtils->getLastUsername();



        return $this->render('login/index.html.twig', [
            'error' => $error,
            'last_username' => $lastUsername
        ]);
    }

    #[Route('/deconnexion', name: 'app_logout', methods: ['GET'])]
    public function logout():never
    {
        throw new \Exception();
    }
}
