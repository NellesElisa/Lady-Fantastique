<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/nous-contacter", name="contact")
     */
    public function create(Request $request)
    {

        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('notice', 'Merci de nous avoir contacté. Notre équipe va vous répondre dans les meilleurs délais.');


            $contactFormData = $form->getData();

            $to_email = 'maurinecouture@gmail.com';
            $to_name = $contactFormData["lastname"];
            $subject= 'Message reçu de : ' .$contactFormData['email'] ;
            $content = 'Sujet : ' .$contactFormData['subject']. "<br/>".
                       'Message : '. "<br/>" .$contactFormData['message'];

           $mail = new Mail();
           $mail->send($to_email,$to_name,$subject,$content);
           $mail->send($contactFormData['email'],"maurinecouture@gmail.com","Nous avons recu votre demande de contact","On vous répondra dans les plus bref delai");
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

}


