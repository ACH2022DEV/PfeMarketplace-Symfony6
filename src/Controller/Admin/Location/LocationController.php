<?php

namespace App\Controller\Admin\Location;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LocationController extends AbstractController
{
    #[Route('/admin/location/{active_index?1}', name: 'app_admin_location')]
    public function index($active_index): Response
    {
        return $this->render('admin/location/index.html.twig', [
            'controller_name' => 'LocationController',
            'active_index' => $active_index
        ]);
    }
}
