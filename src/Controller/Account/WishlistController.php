<?php

namespace App\Controller\Account;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WishlistController extends AbstractController
{
    #[Route('/compte/liste-de-souhait', name: 'app_account_wishlist')]
    public function index(): Response
    {
        return $this->render('account/wishlist/index.html.twig');
    }

    #[Route('/compte/liste-de-souhait/add/{id}', name: 'app_account_wishlist_add')]
    public function add(ProductRepository $productRepository,EntityManagerInterface $entityManager,Request $request, $id): Response
    {
        //Récuperer l'objet du produit souhaité
        $product = $productRepository->findOneById($id);
        //Ajouter le produits  a la wishlist
        if ($product) {
            $this->getUser()->addWishlist($product);

            //Sauvegaarder en BDD
            $entityManager->flush();

        }
            $this->addFlash('success', 'Produit correctement ajouter a votre wishlist');
        return  $this->redirect($request->headers->get('referer'));
    }
        #[Route('/compte/liste-de-souhait/remove/{id}', name: 'app_account_wishlist_remove')]
    public function remove(ProductRepository $productRepository,EntityManagerInterface $entityManager,Request $request, $id): Response
        {
            //Récuperer l'objet du produit a supprimé
            $product = $productRepository->findOneById($id);
            //Ajouter le produits  a la wishlist
            if ($product) {
                $this->addFlash('success', 'Produits correctement supprimer de votre wishlist');
                $this->getUser()->removeWishlist($product);

                //Sauvegaarder en BDD
                $entityManager->flush();
            } else {
                $this->addFlash('danger', 'Produit introuvable dans votre wishlist');
            }
            return  $this->redirect($request->headers->get('referer'));
        }
}
