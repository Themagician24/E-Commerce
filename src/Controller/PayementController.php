<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use http\Header;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PayementController extends AbstractController
{
    #[Route('/commande/paiement{id_order}', name: 'app_payement')]
    public function index($id_order, OrderRepository $orderRepository, EntityManagerInterface $entityManager): Response
    {
        // Initialisation de Stripe avec la clé secrète
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        // Sécurisation de l'URL
        $order = $orderRepository->findOneBy([
            'id' => $id_order,
            'user' => $this->getUser()
        ]);

        // Redirection si la commande n'existe pas
        if (!$order) {
            $this->redirectToRoute('app_home');
        }

        // Préparation des produits pour Stripe
        $products_for_stripe = [];

        foreach ($order->getOrderDetails() as $product) {
            $products_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => number_format($product->getproductPriceWt() * 100, 0, '', ''),
                    'product_data' => [
                        'name' => $product->getProductName(),
                        'images' => [
                            $_ENV['DOMAIN'].'/uploads/'.$product->getProductIllustration()
                        ]
                    ]
                ],
                'quantity' => $product->getProductQuantity(),
            ];

            // Ajout du transporteur
            $products_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => number_format($order->getCarrierPrice() * 100, 0, '', ''),
                    'product_data' => [
                        'name' => 'Transporteur : ' .$order->getCarrierName(),
                    ]
                ],
                'quantity' => 1,
            ];
        }

        // Création de la session de paiement avec Stripe
        $checkout_session = Session::create ([
            'customer_email' => $this->getUser()->getEmail(),
            'line_items' => [[
                $products_for_stripe
            ]],
            'mode' => 'payment',
            'success_url' => $_ENV['DOMAIN']. '/commande/merci/{CHECKOUT_SESSION_ID}',

            // URL d'annulation de la commande
            'cancel_url' =>  $_ENV['DOMAIN'] . '/mon-panier/annulation',
        ]);

        // Attribution de l'ID de session Stripe à la commande et sauvegarde en base de données
        $order->setStripeSessionId($checkout_session->id);
        $entityManager->flush();

        // Redirection vers l'URL de paiement Stripe
        return $this->redirect($checkout_session->url);
    }

    #[Route('/commande/merci/{stripe_session_id}', name: 'app_payement_success')]
    public function success($stripe_session_id, OrderRepository $orderRepository, EntityManagerInterface $entityManager, Cart $cart): Response
    {
        // Récupération de la commande liée à l'ID de session Stripe
        $order = $orderRepository->findOneBy([
            'stripe_session_id' => $stripe_session_id,
            'user' => $this->getUser()
        ]);

        // Redirection si la commande n'existe pas
        if (!$order) {
            $this->redirectToRoute('app_home');
        }

        // Mise à jour de l'état de la commande et vidage du panier si le paiement est réussi
        if ($order->getState() == 1) {
            $order->setState(2);
            $cart->remove(); // Vidage du panier une fois le paiement réussi
            $entityManager->flush();
        }

        // Rendu de la page de succès de paiement
        return $this->render('payement/success.html.twig', [
            'order' => $order,
        ]);
    }
}