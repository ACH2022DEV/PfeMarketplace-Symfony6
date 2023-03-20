<?php

namespace App\Controller\Shared;

use phpDocumentor\Reflection\Type;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route(path: '/login/{type?user}', name: 'app_login', requirements: ['type' => 'user|admin|btob|Seller'])]
    public function login(AuthenticationUtils $authenticationUtils, $type): Response
    {

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        $view = "shared/login/login_".$type.".html.twig";
        return $this->render($view, [
            'controller_name' => 'LoginController',
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }
    #[Route(path: '/Login', name: 'app_login_seller')]
    public function loginSeller(AuthenticationUtils $authenticationUtils): Response
    {

        // get the login error if there is one
       $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
       // $view = "shared/login/login_".$type.".html.twig";
        return $this->render('shared/login/login_Seller.html.twig', [
            'controller_name' => 'LoginController',
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }
}