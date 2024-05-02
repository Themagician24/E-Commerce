<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Repository\UserRepository;
use App\Form\ResetPasswordFormType;
use App\Form\ForgotPasswordFormType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ForgotPasswordController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em) 
    {
        $this->em = $em;
    }

    // Route pour afficher le formulaire de demande de réinitialisation de mot de passe
    #[Route('/mot-de-passe-oublié', name: 'app_password')]

    public function index(Request $request, UserRepository $userRepository): Response
    {
        // Création du formulaire de demande de réinitialisation de mot de passe
        $form = $this->createForm(ForgotPasswordFormType::class);
        $form->handleRequest($request);
        
        // Traitement du formulaire s'il est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {

            // Récupération de l'email renseigné par l'utilisateur
            $email = $form->get('email')->getData();

            // Recherche de l'utilisateur par son email
            $user = $userRepository->findOneByEmail($email);

            // Notification à l'utilisateur
            $this->addFlash('success', "Si votre adresse email existe, 
            vous recevrez un email pour renitialiser votre mot de passe ");

            // Si l'utilisateur existe, génération d'un token, stockage en base de données et envoi d'un email
            if ($user) {
                $token = bin2hex(random_bytes(15));
                $user->setToken($token);
                $date = new DateTime();
                $date->modify('+10 minutes');
                $user->setTokenExpireAt($date);
                $this->em->flush();
                $mail = new Mail();
                $vars = [
                    'link' => $this->generateUrl('app_password_update', 
                    ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL),
                ];
                $mail->send($user->getEmail(), $user->getFirstname().' '.$user->getLastname(), "Modification de votre mot de passe", "forgotpassword.html", $vars);
            }
        }
        // Rendu de la vue avec le formulaire
        return $this->render('password/index.html.twig', [
            'forgotPasswordForm' => $form->createView(),
        ]);
    }

    // Route pour afficher le formulaire de réinitialisation de mot de passe
    #[Route('/mot-de-passe/reset/{token}', name: 'app_password_update')]

    public function update(Request $request,UserRepository $userRepository, $token): Response
    {

        // Redirection si aucun token n'est fourni
        if (!$token) {
            return $this->redirectToRoute('app_password');
        }

        // Recherche de l'utilisateur par le token
        $user = $userRepository->findOneByToken($token);

        // Vérification de la validité du token
        $now = new DateTime();
        if (!$user || $now > $user->getTokenExpireAt()) {
            return $this->redirectToRoute('app_password');

        }


        // Création du formulaire de réinitialisation de mot de passe
        $form = $this->createForm(ResetPasswordFormType::class, $user);
        $form->handleRequest($request);



        // Traitement du formulaire s'il est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {

            $user->setToken(null);
            $user->setTokenExpireAt(null);

            // Traitement à effectuer
            $this->em->flush();
            $this->addFlash(
                'success',
                "<strong>Votre mot de passe est correctement mis à jour!</strong"
            );
            return $this->redirectToRoute('app_login');
        }
        // Rendu de la vue avec le formulaire de réinitialisation
        return $this->render('password/reset.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
