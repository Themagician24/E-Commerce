<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    // Route pour afficher le panier
    #[Route('/mon-panier/{motif}', name: 'app_cart',defaults: [ 'motif' => null ])]
    public function index(Cart $cart, $motif): Response
    {
        if ($motif == "annulation") {
           $this->addFlash(
               'info',
               'Paiement annulé: Vous pouvez mettre à jour votre panier et votre commande '
           );
        }
        // Rendu de la vue du panier avec le contenu du panier
        return $this->render('cart/index.html.twig', [
            'cart' => $cart->getCart(),
            'totalWt' => $cart->getTotalWt()
        ]);
    }

    // Route pour ajouter un produit au panier
    #[Route('/cart/add/{id}', name: 'app_cart_add')]
    public function add($id, Cart $cart, ProductRepository $productRepository,Request $request): Response
    {

        // Récupération du produit par son ID
        $product = $productRepository->findOneById($id);
        // Ajout du produit au panier
        $cart->add($product);

        // Ajout d'un message flash pour informer l'utilisateur
        $this->addFlash(
            'primary',
            "Vous venez d'ajouter un nouvel article dans votre panier"
        );

        // Redirection vers la page du produit
        return $this->redirect($request->headers->get('referer'));
    }

    // Route pour ajouter un produit au panier
    #[Route('/cart/decrease/{id}', name: 'app_cart_decrease')]
    public function decrease($id, Cart $cart): Response
    {

        $cart->decrease($id);

        // Ajout d'un message flash pour informer l'utilisateur
        $this->addFlash(
            'danger',
            "Produit correctement supprimé de votre panier"
        );

        // Redirection vers la page du produit
        return $this->redirectToRoute('app_cart');
    }

    // Route pour supprimer un produit au panier
    #[Route('/cart/remove', name: 'app_cart_remove')]
    public function remove( Cart $cart): Response
    {
        $cart->remove();

        // Redirection vers la page du produit
        return $this->redirectToRoute('app_home');

    }
}