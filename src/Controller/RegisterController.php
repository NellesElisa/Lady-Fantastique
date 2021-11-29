<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager=$entityManager;
    }

    /**
     * @Route("/inscription", name="register")
     */
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {

        $notification = null;
        $user = new User();
        $form = $this-> createForm(RegisterType::class,$user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

           $user = $form->getData();

           $search_email = $this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());

           if(!$search_email){

            $password = $passwordHasher->hashPassword($user,$user->getPassword());

            $user->setPassword($password);
 
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $mail= new Mail();
            $content = "Bonjour " . $user->getFirstname() ;
            $mail->send($user->getEmail(), $user->getFirstname(), 'bienvenue sur lady fantastique',$content);

            $notification = " votre inscription s'est correctement déroulée";
           }else{
            $notification = "L'email existe déja ";
           }

        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification,
        ]);
    }
}
