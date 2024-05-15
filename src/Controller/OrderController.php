<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{

    /**
     * 1ère étape du tunnel d'achat
     * choix de l'adresse de livraison et du transporteur
     *
     */
    
    #[Route('/commande/livraison', name: 'app_order')]
    public function index(): Response
    {
        // Récupérer les adresses de livraison de l'utilisateur
        $addresses = $this->getUser()->getAdresses();

        // Rediriger vers la page de création d'adresse si aucune adresse n'est enregistrée
        if (count($addresses) == 0) {
            return $this->redirectToRoute('app_account_address_form');
        }

        // Créer le formulaire de choix de l'adresse de livraison et du transporteur
        $form = $this->createForm(OrderType::class, null, [
           'addresses' => $addresses,
            'action' => $this->generateUrl('app_order_summary')
        ]);

        // Rendre la vue avec le formulaire
        return $this->render('order/index.html.twig', [
            'deliveryForm' => $form->createView(),
        ]);
    }

    /**
     * 2eme étape du tunnel d'achat
     * recap de la commande de l'utilisateur
     * Insertion en BDD
     * Preparation du payement vers Stripe
     */

     
    #[Route('/commande/racapitulatif', name: 'app_order_summary')]
    public function add(Request $request, Cart $cart, EntityManagerInterface $entityManager): Response
    {
        // Si l'utilisateur accède directement à cette page sans passer par le formulaire de la page précédente, rediriger
        if ($request->getMethod() != 'POST') {
            return $this->redirectToRoute('app_cart');
        }

        // Récupérer les produits du panier
        $products = $cart->getCart();

        // Créer le formulaire avec les adresses disponibles
        $form = $this->createForm(OrderType::class, null, [
            'addresses' => $this->getUser()->getAdresses(),
        ]);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide, enregistrer la commande en BDD
        if ($form->isSubmitted() && $form->isValid()) {
            // Stocker les informations en BDD

            // Création de la chaîne d'adresse de livraison
            $addressObj = $form->get('addresses')->getData();
            $address = $addressObj->getFirstname(). ' '.$addressObj->getLastname() .'<br/>';
            $address .= $addressObj->getAdresse() .'<br/>';
            $address .= $addressObj->getPostal(). ' '.$addressObj->getCity() .'<br/>';
            $address .= $addressObj->getCountry() .'<br/>';
            $address .= $addressObj->getPhone() .'<br/>';

            // Création de la commande
            $order = new Order();
            $order->setUser($this->getUser());
            $order->setCreatedAt(new \DateTime());
            $order->setState(1);
            $order->setCarrierName($form->get('carriers')->getData()->getName());
            $order->setCarrierPrice($form->get('carriers')->getData()->getPrice());
            $order->setDelivery($address);

            // Ajout des détails de commande
            foreach ($products as $product) {
                $orderDetail = new OrderDetail();
                $orderDetail->setProductName($product['object']->getName());
                $orderDetail->setProductIllustration($product['object']->getIllustration());
                $orderDetail->setProductPrice($product['object']->getPrice());;
                $orderDetail->setProductTva($product['object']->getTva());;
                $orderDetail->setProductQuantity($product['qty']);
                $order->addOrderDetail($orderDetail);
            }

            // Enregistrer la commande en BDD
            $entityManager->persist($order);
            $entityManager->flush();
        }

        // Rendre la vue du récapitulatif de commande
        return $this->render('order/summary.html.twig', [
            'choices' => $form->getData(),
            'cart' => $products,
            'order' => $order,
            'totalWt' => $cart->getTotalWt()
        ]);
    }
}