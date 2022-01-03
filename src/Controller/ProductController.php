<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Search;
use App\Entity\OrderDetails;
use App\Entity\Product;
use App\Form\SearchCatType;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager= $entityManager;
    }

    /**
     * @Route("/nos-produits", name="products")
     */
    public function index(Request $request,Cart $cart): Response
    {
        $products = $this->entityManager->getRepository(Product::class)->findAll();

        $search = new Search();
        $form = $this->createForm(SearchType::class,$search);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $products = $this->entityManager->getRepository(Product::class)->findWithSearch($search);
        }

        return $this->render('product/index.html.twig',[
            'products' => $products,
            'form' => $form->createView(),
            'cart' => $cart->getFull(),
        ]);
    }


    /**
    * @Route("/produit/{slug}", name="product")
    */
   public function show($slug, Cart $cart)
   {
       $product = $this->entityManager->getRepository(Product::class)->findOneBy(array('slug' => $slug));
       $products = $this->entityManager->getRepository(Product::class)->findByIsBest(1);
       $allProducts = $this->entityManager->getRepository(Product::class)->findAll();

       if (!$product) {
           return $this->redirectToRoute('products');
       }

//       calcul du stock total de produit
//       $stocktotal = 0;
//       for ($i = 0; $i < count($allProducts); $i++) {
//           $stocktotal = $stocktotal + $allProducts[$i]->getStock();
//       }


       return $this->render('product/show.html.twig',[
           'product' => $product,
           'slug'=> $slug,
           'products'=>$products,
           'virtuel'=>$cart->stockVirtuel(),
//           'stockTotal' => $stocktotal,
           'cart' => $cart->getFull(),
       ]);
   }

}
