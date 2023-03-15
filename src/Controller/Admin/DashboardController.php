<?php

namespace App\Controller\Admin;

use App\Repository\MenuItemAdminRepository;
use App\Service\Helpers;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/admin', name: 'app_admin_')]
class DashboardController extends AbstractController
{
    public function __construct(private RequestStack $requestStack,
                                private MenuItemAdminRepository $menuItemAdminRepository,
                                private Security $security,
                                private Helpers $helpers
    ){}


    #[Route('/dashboard', name: 'dashboard' ) , IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        $user = $this->security->getUser();
        $session = $this->requestStack->getSession();

        //if(!$session->has('menu')){ // Uncomment to get menu from session if exists.
            if($this->isGranted('ROLE_SUPER_ADMIN')) {
                $menu_object = $this->menuItemAdminRepository->findBy([], ['displayOrder' => 'ASC']);
                $menu = $this->helpers->convert_ObjectArray_to_2DArray($menu_object);
            }else{ // ROLE_ADMIN
                $menu = $this->menuItemAdminRepository->find_innerJoin();
            }
            $menu_as_tree = $this->helpers->buildTree($menu);
            if(array_key_exists('ADMIN', $menu_as_tree))
                $session->set('menu' , $menu_as_tree['ADMIN']['children']);
        //}
        return $this->render('admin/dashboard/index.html.twig', [
            'controller_name' => 'DashboardController'
        ]);
    }



}
