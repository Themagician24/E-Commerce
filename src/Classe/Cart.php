<?php

namespace App\Classe;
use Symfony\Component\HttpFoundation\RequestStack;

class Cart
{
    // Constructeur de la classe Cart
    public function __construct(private RequestStack $requestStack)
    {
        // Initialisation de la classe avec RequestStack
    }

    // Méthode pour ajouter un produit au panier
    public function add($product)
    {
        // Appeler la session de Symfony
        $cart = $this->requestStack->getSession()->get('cart');

        // Ajouter un qtity +1 à mon produit
        if (isset($cart[$product->getId()])) {
            $cart[$product->getId()] = [
                'object' => $product,
                'qty' => $cart[$product->getId()]['qty'] + 1
            ];
        }else {
            $cart[$product->getId()] = [
                'object' => $product,
                'qty' => 1
            ];
        }

        // Créer ma session
        $this->requestStack->getSession()->set('cart', $cart);
    }
    //Fonction permettant la suppression d'n produit au panier
    public function decrease($id)
    {
        $cart = $this->requestStack->getSession()->get('cart');

        if ($cart[$id]['qty'] > 1) {
            $cart[$id]['qty'] = $cart[$id]['qty'] - 1;
        }else {
            unset($cart[$id]);
        }

        $this->requestStack->getSession()->set('cart', $cart);
    }
    public function fullQuantity()
    {
        $cart = $this->requestStack->getSession()->get('cart');
        $quantity = 0;

        // Vérifier si $cart est null avant de l'itérer
        if ($cart !== null) {
            foreach ($cart as $product) {
                $quantity += $product['qty'];
            }
        }

        return $quantity;
    }

    //Function permettant d'afficher le prix total des produits dans mon panier
    public function getTotalWt()
    {
        $cart = $this->requestStack->getSession()->get('cart');
        $price = 0;

        // Vérifier si $cart est null avant de l'itérer
        if ($cart !== null) {
            foreach ($cart as $product) {
                $price = $price + ($product['object']->getPriceWt() * $product['qty']);
            }
        }

        return $price;
        dd($price);
    }


    public function remove()
    {
      return $this->requestStack->getSession()->remove('cart');
    }

    // Méthode pour récupérer le panier
    public function getCart()
    {
        return $this->requestStack->getSession()->get('cart');
    }
}
