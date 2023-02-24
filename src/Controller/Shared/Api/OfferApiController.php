<?php

namespace App\Controller\Shared\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class OfferApiController extends AbstractController
{
    #[Route('/shared/api/offer/api', name: 'app_shared_api_offer_api')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/Shared/Api/OfferApiController.php',
        ]);
    }
}
