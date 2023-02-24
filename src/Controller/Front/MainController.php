<?php

namespace App\Controller\Front;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class MainController extends AbstractController
{
    public function __construct(
        private Security $security,
        private RequestStack $requestStack)
    {
    }

    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        $user = $this->security->getUser();
        $session = $this->requestStack->getSession();


        if($this->isGranted('ROLE_ADMIN')){
            if($this->isGranted('ROLE_SUPER_ADMIN')) {
                $auth_agencies = [];// $this->agencyRepository->findAll();
            }
            else { // ROLE_ADMIN
                $auth_agencies = [];
            }
            $session->set('auth_agencies' , $auth_agencies);
            return $this->redirectToRoute('app_admin_dashboard');
        }

        if($this->isGranted('ROLE_SELLER')){
            return $this->redirectToRoute('app_seller_dashboard');
        }

        /*if($user) {
            $roles = $user->getRoles();
            //dd($user);
            switch ($roles[0]){
                case "ROLE_SUPER_ADMIN": // 3T User
              //      dd('here');
                case "ROLE_ADMIN": // Agency Administrator or Agency Head (Chef)


                case "ROLE_B2B":
                case "ROLE_B2B_SUB":
                case "ROLE_B2CORP":
                case "ROLE_B2CORP_ADH":
                case "ROLE_B2C":
                return $this->render('front/main/index.html.twig', [
                    'controller_name' => $user->getUserIdentifier(),
                ]);
            }
        }*/
        return $this->render('front/main/index.html.twig');
    }
}
