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
    public function index(Request $request): Response
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
        ]);
    }


    /**
    * @Route("/produit/{slug}", name="product")
    */
   public function show($slug)
   {
       $product = $this->entityManager->getRepository(Product::class)->findOneBy(array('slug' => $slug));
       $products = $this->entityManager->getRepository(Product::class)->findByIsBest(1);

       if (!$product) {
           return $this->redirectToRoute('products');
       }

       return $this->render('product/show.html.twig',[
           'product' => $product,
           'slug'=> $slug,
           'products'=>$products,
       ]);
   }


    /**
     * @Route("/produit/{slug}/rupture", name="rupture")
     */
   public function stock($slug){
       $product = $this->entityManager->getRepository(Product::class)->findOneBy(array('slug' => $slug));
       $products =$this->entityManager->getRepository(Product::class)->findByIsBest(1);

       return $this->render('product/rupture.html.twig',[
           'product' => $product,
           'slug'=> $slug,
           'products'=>$products,
       ]);

   }


//    /**
//     * @Route("/produit/{slug}/supprimer-un-produit", name="product_delete")
//     */
//    public function delete($slug)
//    {
//        // je voudrai qu'elle supprime un produit du stock lorsqu'il est ajoute au panier
//        $product = $this->entityManager->getRepository(Product::class)->findOneBy(array('slug' => $slug));
//
//        if ($product && $product->getStock() ) {
//            $this->entityManager->remove($product);
//            $this->entityManager->flush();
//        }
//
//        return $this->redirectToRoute('products');
//
//    }



}
