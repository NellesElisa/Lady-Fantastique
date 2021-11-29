<?php
// identif laboutiquefrance@yopmail.com
//mdp $^poiuytreza098

namespace App\Controller;

    use Stripe\Stripe;
    use App\Classe\Cart;
    use App\Entity\Order;
    use App\Entity\Product;
    use Stripe\Checkout\Session;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{
    /**
     * @Route("/commande/create-session/{reference}", name="stripe_create_session")
     */
    public function index(Cart $cart, $reference, EntityManagerInterface $entityManager, Request $request): Response
    {

        $products_for_stripe = [];
        $YOUR_DOMAIN = $request->getSchemeAndHttpHost();

        $order = $entityManager->getRepository(Order::class)->findOneByReference($reference);


        if (!$order) {
            new JsonResponse(['error' => 'order']);
        }


        foreach ($order->getOrderDetails()->getValues() as $product) {


            $products_for_stripe[] = [

                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product->getPrice(),
                    'product_data' => [
                        'name' => $product->getProduct(),
                        'images' => [$YOUR_DOMAIN],
                    ],
                ],
                'quantity' => $product->getQuantity(),
            ];

        }

        $products_for_stripe[] = [

            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getCarrierPrice(),
                'product_data' => [
                    'name' => $order->getCarrierName(),
                    'images' => [$YOUR_DOMAIN],
                ],
            ],
            'quantity' => 1,
        ];

        Stripe::setApiKey('sk_test_51JpXQwEPeWS22cBu93EPyF13NUZzEcT8luLdWudWwpseIeerDRwxHQAsAjzijRyynwZKmVqRON8c2A371YqzxH7000ljnXW0XB');
        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [[
                $products_for_stripe
            ]],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/commande/erreur/{CHECKOUT_SESSION_ID}',
        ]);

        $order->setStripeSessionId($checkout_session->id);
        $entityManager->flush();

        $response = new JsonResponse(['id' => $checkout_session->id]);
        return $response;
    }
}