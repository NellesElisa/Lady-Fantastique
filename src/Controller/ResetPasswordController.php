<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\ResetPassword;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager= $entityManager;
    }
    /**
     * @Route("/mot-de-passe-oublie/password", name="reset_password")
     */
    public function index(Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        if($request->get('email')){
           $user = $this->entityManager->getRepository(User::class)->findOneByEmail($request->get('email'));
           
        if($user){
            // enregister en base la demande de reset password avec user,token et created at
            $reset_password = new ResetPassword();
            $reset_password->setUser($user);
            $reset_password->setToken(uniqid());
            $reset_password->setCreatedAt(new DateTimeImmutable());
            $this->entityManager->persist($reset_password);
            $this->entityManager->flush();

            // envoyer un email a l'utilisateur pour reinitialiser son mot de passe
            $url= $this->generateUrl('update_password',[
                'token'=> $reset_password->getToken()
                ]);

            $content ="Bonjour". $user->getFirstname()."<br/> Vous avez demandé à reinitialiser votre mot de passe <br>";
            $content .= "merci de bien vouloir cliquer sur le lien suivant pour mettre a jour <a href='".$url."'> votre mot de passe</a>";

            $mail = new Mail();
            $mail->send($user->getEmail(),$user->getFirstname(). ' ' .$user->getLastname(),'Reinitialiser votre mot de passe',$content);

            $this->addFlash('notice', 'Vous allez recevoir un mail pour reinitialiser votre mot de passe');

        }else{
            $this->addFlash('notice', 'Cette adresse email est inconnue');
        }
    }
        return $this->render('reset_password/index.html.twig', [
           
        ]);
    }


      /**
     * @Route("/modifier-mot-de-passe-oublie/{token}", name="update_password")
     */
    public function update(Request $request, $token, UserPasswordHasherInterface $passwordHasher){
        $reset_password= $this->entityManager->getRepository(ResetPassword::class)->findOneByToken($token);

        if (!$reset_password) {
            return $this->redirectToRoute('reset_password');
        }

        //verifier si le created at == now - 3h
        $now=new DateTimeImmutable();
        if($now > $reset_password->getCreatedAt()->modify('+ 3 hour')){

            $this->addFlash('notice', 'votre demande de mot de passe a expiré');
            return $this->redirectToRoute('reset_password'); 
        }

        //rendre une vue avec mot de passe et confirmer votre mot de passe
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $new_pwd =$form->get('new_password')->getData();

        //encodage
           $password = $passwordHasher->hashPassword($reset_password->getUser(), $new_pwd);
           $reset_password->getUser()->setPassword($password);
        // flush
           $this->entityManager->flush();
        //redicrection de l'utilisateur 
         $this->addFlash('notice', 'Votre mot de passe à bien été mis à jour ');
         return $this->redirectToRoute('app_login');
        

        }



        return $this->render('reset_password/update.html.twig', [
           'form'=>$form->createView()
        ]);
        
        
        dd($reset_password);
    }
}
