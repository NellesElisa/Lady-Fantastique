<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderSuccessController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
       $this->entityManager=$entityManager;
    }

    /**
     * @Route("/commande/merci/{stripeSessionId}", name="order_success")
     */
    public function index(Cart $cart ,$stripeSessionId)
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('home');
        }

        if($order->getState() == 0){
            //vider la sessoin cart
            $cart->remove();

            //modifier le statut isPaid de notre commande
            $order->setState(1);
            $this->entityManager->flush();

            // envoyer un mail
            
            $mail= new Mail();
            $content = "Bonjour " . $order->getUser()->getFirstname()."<br/>Merci de votre commande. <br/>" . "<br/> Lorem ipsum dolor sit, amet consectetur adipisicing elit. Minima assumenda totam earum quos in, repellat voluptatum explicabo unde ut illum est dignissimos modi tenetur repellendus inventore delectus nobis quaerat ex.<br/>" ;
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(), 'Votre commande La boutique frnacaise est bien validée',$content);

        }
        

        return $this->render('order_success/index.html.twig',[
            'order' => $order
        ]);
    }
}