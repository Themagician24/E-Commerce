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
        $addresses = $this->getUser()->getAdresses();
            if (count($addresses) == 0) {
                return $this->redirectToRoute('app_account_address_form');
            }


        $form = $this->createForm(OrderType::class, null, [
           'addresses' => $addresses,
            'action' => $this->generateUrl('app_order_summary')
        ]);


        return $this->render('order/index.html.twig', [
            'deliveryForm' => $form->createView(),
        ]);
    }
    /**
     * 2eme étape du tunnel d'achat
     * recap de la commande de l'utilisateur
     *Insertion en BDD
     * Preparation du payement vers Stripe
     */
    #[Route('/commande/racapitulatif', name: 'app_order_summary')]
    public function add(Request $request, Cart $cart, EntityManagerInterface $entityManager): Response
    {
        //Si l'utilisateur clic juste sur entrer sans raffraichir la page il va se prendre une erreur alors pour eviter
        //nous allons le renvoyer a la page precedente d'achat

        if ($request->getMethod() != 'POST') {
            return $this->redirectToRoute('app_cart');
        }
        $products = $cart->getCart();


        $form = $this->createForm(OrderType::class, null, [
            'addresses' => $this->getUser()->getAdresses(),
        ]);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid() ) {
            //Stocker les informations en BDD

            //Creation de la chaine adresse
            $addressObj = $form->get('addresses')->getData();
            $address = $addressObj->getFirstname(). ' '.$addressObj->getLastname() .'<br/>';
            //On va utiliser . = qui veux dire:prends la valeur precedente et ajoute la a celle ci
            $address .= $addressObj->getAdresse() .'<br/>';
            $address .= $addressObj->getPostal(). ' '.$addressObj->getCity() .'<br/>';
            $address .= $addressObj->getCountry() .'<br/>';
            $address .= $addressObj->getPhone() .'<br/>';


            $order = new Order();
            $order->setUser($this->getUser());
            $order->setCreatedAt(new \DateTime());
            $order->setState(1);
            $order->setCarrierName($form->get('carriers')->getData()->getName());
            $order->setCarrierPrice($form->get('carriers')->getData()->getPrice());
            $order->setDelivery($address);

            foreach ($products as $product) {

                $orderDetail = new OrderDetail();
                $orderDetail->setProductName($product['object']->getName());
                $orderDetail->setProductIllustration($product['object']->getIllustration());
                $orderDetail->setProductPrice($product['object']->getPrice());;
                $orderDetail->setProductTva($product['object']->getTva());;
                $orderDetail->setProductQuantity($product['qty']);
                $order->addOrderDetail($orderDetail);

            }
            $entityManager->persist($order);
            $entityManager->flush();
        }

        return $this->render('order/summary.html.twig', [
            'choices' => $form->getData(),
            'cart' => $products,
            'order' => $order,
            'totalWt' => $cart->getTotalWt()
        ]);
    }
}
