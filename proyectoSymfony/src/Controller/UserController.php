<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\User;
use App\Form\RegisterType;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    public function register(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        //crear formulario
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        //llenar el fomulario con los datos del objeto
        $form->handleRequest($request);

        //si envia el formulario
        if($form->isSubmitted() && $form->isValid()) {
            //modifica el objeto para guardar
            $user->setRole('ROLE_USER');
            //$date_now = (new \DateTime())->format('d-m-y:i:s');
           
            $user->setCreatedAt(new \DateTime('now'));
            //cofrar contraseña
            $encoded = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encoded);
            
            //guardar user
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('tasks');
            
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    public function login(AuthenticationUtils $authenticationUtils){
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();
        
        return $this->render('user/login.html.twig', array(
            'error' => $error,
            'last_username' => $lastUsername          
        ));        
    }
}
