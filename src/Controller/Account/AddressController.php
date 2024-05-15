<?php

namespace App\Controller\Account;

// Importation des classes nécessaires
use App\Classe\Cart;
use App\Entity\Adresse;
use App\Form\AdresseUserType;
use App\Repository\AdresseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Définition de la classe AddressController qui étend AbstractController
class AddressController extends AbstractController
{
    // Déclaration de la propriété entityManager
    private $entityManager;

    // Constructeur de la classe qui injecte EntityManagerInterface
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // Route pour afficher la liste des adresses
    #[Route('/compte/adresses', name: 'app_account_addresses')]
    public function index(): Response
    {
        // Renvoie la vue 'account/address/index.html.twig'
        return $this->render('account/address/index.html.twig');
    }

    // Route pour supprimer une adresse
    #[Route('/compte/adresses/delete/{id}', name: 'app_account_address_delete')]
    public function delete($id, AdresseRepository $adresseRepository): Response
    {
        // Récupère l'adresse par son ID
        $address = $adresseRepository->findOneById($id);
        // Vérifie si l'adresse existe et si elle appartient à l'utilisateur actuel
        if (!$address or $address->getUser() != $this->getUser()) {
            // Redirige vers la liste des adresses si l'adresse n'existe pas ou ne lui appartient pas
            return $this->redirectToRoute('app_account_addresses');
        }
        // Ajoute un message flash pour informer l'utilisateur de la suppression
        $this->addFlash(
            'success',
            "Votre adresse a été correctement supprimée"
        );
        // Supprime l'adresse de la base de données
        $this->entityManager->remove($address);
        $this->entityManager->flush();
        // Redirige vers la liste des adresses
        return $this->redirectToRoute('app_account_addresses');
    }

    // Route pour ajouter ou modifier une adresse
    #[Route('/compte/adresse/ajouter/{id}', name: 'app_account_address_form', defaults: ['id'=> null] )]
    public function form(Request $request, $id, AdresseRepository $adresseRepository, Cart $cart): Response
    {
        // Si un ID est fourni, récupère l'adresse existante
        if ($id) {
            $address = $adresseRepository->findOneById($id);
            // Vérifie si l'adresse existe et si elle appartient à l'utilisateur actuel
            if (!$address OR $address->getUser() != $this->getUser()) {
                // Redirige vers la liste des adresses si l'adresse n'existe pas ou ne lui appartient pas
                return $this->redirectToRoute('app_account_addresses');
            }
        } else {
            // Crée une nouvelle instance d'Adresse et l'associe à l'utilisateur actuel
            $address = new Adresse();
            $address->setUser($this->getUser());
        }

        // Crée le formulaire pour l'adresse
        $form = $this->createForm(AdresseUserType::class, $address);

        // Traite la requête du formulaire
        $form->handleRequest($request);

        // Vérifie si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Persiste l'adresse dans la base de données
            $this->entityManager->persist($address);
            $this->entityManager->flush();

            // Ajoute un message flash pour informer l'utilisateur de la sauvegarde
            $this->addFlash(
                'success',
                "Votre adresse est correctement sauvegardée"
            );
            // Redirige vers la page de commande si le panier contient des articles
            if ($cart->fullQuantity() > 0) {
                return $this->redirectToRoute('app_order');
            }

            // Redirige vers la liste des adresses
            return $this->redirectToRoute("app_account_addresses");
        }

        // Renvoie la vue 'account/address/form.html.twig' avec le formulaire
        return $this->render('account/address/form.html.twig', [
            'addressForm' => $form
        ]);
    }
}