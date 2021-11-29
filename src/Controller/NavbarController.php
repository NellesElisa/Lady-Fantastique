<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Search;
use App\Entity\Product;
use App\Form\SearchCatType;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NavbarController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/navbar", name="navbar")
     * @param Request $request
     */
    public function index(Request $request , Cart $cart)
    {
        $search = new Search();

        $form = $this->createForm(SearchType::class, $search); //fonctionne avec searchCatType mais pas avec searchType !!!!
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $products = $this->entityManager->getRepository(Product::class)->findWithSearch($search);

            return $this->render('product/index.html.twig', [
                'products' => $products,
                'form' => $form->createView(),
            ]);

        } else {
            $products = $this->entityManager->getRepository(Product::class)->findAll();
        }

        return $this->render('navbar/index.html.twig', [
            'products' => $products,
            'cart'=> $cart->getfull(),
            'form' => $form->createView(),
        ]);
    }
}
