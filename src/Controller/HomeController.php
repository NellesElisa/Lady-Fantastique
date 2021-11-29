<?php

namespace App\Controller;


use App\Classe\Cart;
use App\Classe\Search;
use App\Entity\Header;
use App\Entity\Product;
use App\Form\SearchCatType;
use App\Form\SearchType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(Request $request): Response
    {
        $headers = $this->entityManager->getRepository(Header::class)->findAll();

        $search = new Search();
        $formCat = $this->createForm(SearchCatType::class, $search);
        $formCat->handleRequest($request);

        if ($formCat->isSubmitted() && $formCat->isValid()) {

                $products = $this->entityManager->getRepository(Product::class)->findWithSearch($search);

                return $this->render('product/index.html.twig', [
                    'products' => $products,
                    'form' => $formCat->createView(),
                ]);
            } else {
                $products = $this->entityManager->getRepository(Product::class)->findByIsBest(1);
            }
            return $this->render('home/index.html.twig', [
                'products' => $products,
                'headers' => $headers,
                'form' => $formCat->createView(),
            ]);
        }
}
