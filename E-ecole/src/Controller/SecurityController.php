<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    //FONCTION PAGE LOGIN
    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastLogin = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', [
            'controller_name' => 'SecurityController',
            'last_username' => $lastLogin,
            'error' => $error
        ]);
    }

    //FONCTION QUI RECUPERER LE ROLE DE L'UTILISATEUR APRES LE LOGIN ET LE REDIRIGE EN FONCTION DU ROLE
    /**
     * @Route("/redirect", name="redirect_login")
     */
    public function loginRedirect(){
        $role = $this->getUser()->getRole();
        if($role == "ROLE_ADMIN"){
            return $this->redirectToRoute('admin');
        }
        if($role == "ROLE_ENSEIGNANT"){
            return $this->redirectToRoute('enseignant');
        }
        if($role == "ROLE_ETUDIANT"){
            return $this->redirectToRoute('etudiant');
        }
        if($role == "ROLE_BLOG"){
            return $this->redirectToRoute('etudiant');
        }

    }
}
