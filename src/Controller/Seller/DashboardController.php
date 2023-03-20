<?php

namespace App\Controller\Seller;

use App\Entity\Seller;
use App\Repository\MenuItemAdminRepository;
use App\Repository\MenuItemSellerRepository;
use App\Service\Helpers;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

#[Route('/seller', name: 'app_seller_')]
class DashboardController extends AbstractController
{  private $em;
    public function __construct(private RequestStack $requestStack,
                                private MenuItemSellerRepository $menuItemSellerRepository,
                                private Security $security,
                                private Helpers $helpers,private ManagerRegistry $doctrine
    ){ $this->em=$doctrine;}


    #[Route('/dashboard_Seller', name: 'dashboard2' )]
    //, IsGranted('ROLE_SELLER')]
    public function index(Request $request, Security $security): Response
    {
      //  $session = $this->requestStack->getSession();
        $session = $request->getSession();

        if ( $session->isStarted()) {
            //$userId= $session->get('id');
         //   $userId = $session->get('_security_main');
            //$userId = $event->getAuthenticationToken()->getUser();
            $user = $this->security->getUser();
            $userId = $user->getId();
            $seller2 = $this->em->getRepository(Seller::class)
                ->findSellerByUserId($userId);
//            $token = $session->get('_security_main');
//            $user = $token->getUser();
//            $userId = $user->getId();

            // $session->set('id', '555');

            //   $userId = $request->getSession()->get('id');
          //  $user = $this->getUser('security.token_storage')->getToken()->getUser();
            //$userId = $request->getSession()->get('_security_main')->get('id');

            /*  $seller = $this->em->getRepository(Seller::class)
                  ->findSellerByUserId($userId);*/


        }

        //if(!$session->has('menu')){ // Uncomment to get menu from session if exists.
        //commenter pour ce moment pour l'affichage de design
          /*  if($this->isGranted('ROLE_SELLER')) {
                $menu_object = $this->menuItemSellerRepository->findBy([], ['displayOrder' => 'ASC']);
                $menu = $this->helpers->convert_ObjectArray_to_2DArray($menu_object);
            }else{ // ROLE_ADMIN
                $menu = $this->menuItemSellerRepository->find_innerJoin();
            }
            $menu_as_tree = $this->helpers->buildTree($menu);
            if(array_key_exists('ADMIN', $menu_as_tree))
                $session->set('menu' , $menu_as_tree['ADMIN']['children']);*/
        //}
        return $this->render('seller/dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'seller' => $seller2,
              'userId' => $userId
        ]);
    }
}
