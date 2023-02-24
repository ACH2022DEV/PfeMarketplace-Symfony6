<?php

namespace App\Controller\Seller;

use App\Repository\MenuItemAdminRepository;
use App\Repository\MenuItemSellerRepository;
use App\Service\Helpers;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/seller', name: 'app_seller_')]
class DashboardController extends AbstractController
{
    public function __construct(private RequestStack $requestStack,
                                private MenuItemSellerRepository $menuItemSellerRepository,
                                private Security $security,
                                private Helpers $helpers
    ){}


    #[Route('/dashboard', name: 'dashboard' ) , IsGranted('ROLE_SELLER')]
    public function index(): Response
    {
        $user = $this->security->getUser();
        $session = $this->requestStack->getSession();

        //if(!$session->has('menu')){ // Uncomment to get menu from session if exists.
            if($this->isGranted('ROLE_SELLER')) {
                $menu_object = $this->menuItemSellerRepository->findBy([], ['displayOrder' => 'ASC']);
                $menu = $this->helpers->convert_ObjectArray_to_2DArray($menu_object);
            }else{ // ROLE_ADMIN
                $menu = $this->menuItemSellerRepository->find_innerJoin();
            }
            $menu_as_tree = $this->helpers->buildTree($menu);
            if(array_key_exists('ADMIN', $menu_as_tree))
                $session->set('menu' , $menu_as_tree['ADMIN']['children']);
        //}
        return $this->render('seller/dashboard/index.html.twig', [
            'controller_name' => 'DashboardController'
        ]);
    }
}
