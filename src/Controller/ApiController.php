<?php

namespace App\Controller;

use App\Entity\Airport;
use App\Entity\Customer;
use App\Entity\CustomerXmlLink;
use App\Entity\User;
use App\Entity\XmlIpAddress;
use App\Service\Amadeus\SearchShopping\AirportRoutes;
use App\Service\MainFlight;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{

    #[Route('/api', name: 'app_api')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $method = '';
        $IP_ADDRESS = '';
        $response = null;
        $error ='';
        $message = '';
        $searchCode = '';
        $response = null;
        $ClientCode = '';


        return $this->json(['method' => $method,
            'error' => $error,
            'message' => $message,
            'ip_address' => $IP_ADDRESS,
            'customer_id' => $ClientCode,
            'searchCode' => $searchCode,
            'response' => $response,
        ]);


    }

    #[Route('/api/post_api', name: 'app_post_api', methods: ['POST'])]
    public function post_api(Request $request, EntityManagerInterface $em): Response
    {
      /*

//        $this->em = $this->getDoctrine()->getManager();
        $em->persist($object);
        $em->flush();*/

        return $this->json([
            'message' => 'Inserted Successfully',
        ]);
    }


    #[Route('/api/update_api/{id}', name: 'app_update_api', methods: ['PUT'])]
    public function update_api(Request $request, $id, EntityManagerInterface $em): Response
    {
       /*
        // $em = $this->getDoctrine()->getManager();
        $em->persist($objeect);
        $em->flush();*/

        return $this->json([
            'message' => 'Updated Successfully',
        ]);
    }

    #[Route('/api/delete_api/{id}', name: 'app_delete_api', methods: ['DELETE'])]
    public function delete_api(Request $request, $id, EntityManagerInterface $em): Response
    {
        $method = '';
        $message = '';
        $error = false;
        $IP_ADDRESS = "";
        $ClientCode = '';
        $devise = 'TND';
        $language = 'fr';
        $response = [];
        $searchCode = "";




        return $this->json(['method' => $method,
            'error' => $error,
            'message' => $message,
            'ip_address' => $IP_ADDRESS,
            'customer_id' => $ClientCode,
            'searchCode' => $searchCode,
            'response' => $response,
        ]);
    }

    #[Route('/api/fetchall_api', name: 'app_fetchall_api', methods: ['GET'])]
    public function fetchall_api(Request $request, $id, EntityManagerInterface $em): Response
    {
       $list = $em->getRepository(Object::class) . findAll();
        return $this->json($list);
    }


}
