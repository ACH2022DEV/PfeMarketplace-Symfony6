<?php

namespace App\Controller\Seller;


use App\Controller\Shared\SellerOfferController;
use App\Repository\SellerOfferRepository;
use App\Service\Helpers;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
//use http\Client;
use GuzzleHttp\Client;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;



#[Route('/ApiHotel', name: 'app_Api_Hotel')]
class Api_HotelController extends AbstractController
{

    private $httpClient;
    private $sellerOfferController;
    private $urlGenerator;
    private  $apiData ;
    public function __construct( HttpClientInterface $httpClient,SellerOfferController $sellerOfferController,UrlGeneratorInterface $urlGenerator)
    {
        $this->httpClient = $httpClient;
        $this->sellerOfferController=$sellerOfferController;
        $this->urlGenerator = $urlGenerator;


    }
    #[Route('/getApiHotel', name: 'ApiHotel_list', methods: ['GET','POST'])]
    public function getApi_hotel(HttpFoundationRequest $httpFoundationRequest, SerializerInterface $serializer, SellerOfferRepository $sellerOfferRepository): Response
    {
        $client = new Client();
        $headers = $httpFoundationRequest->headers->all();

        // create a new GuzzleHttp\Psr7\Request instance
        $guzzleRequest = new Request(
            $httpFoundationRequest->getMethod(),
            $httpFoundationRequest->getUri(),
            $headers,
            $httpFoundationRequest->getContent()
        );

        $responseOfSellersValides = $this->sellerOfferController->getSellerOffers_lists($serializer, $sellerOfferRepository)->getContent();
        $sellersValides = json_decode($responseOfSellersValides, true);

        //get information about searchHotel from flutter
        $searchHotel = json_decode($guzzleRequest->getBody(), true);
        /*$checkInDate = $searchHotel['checkIn'];
        $checkOutDate = $searchHotel['checkOut'];
        $_city = $searchHotel['city'];
        $occupancies = $searchHotel['occupancies'];*/

        $guzzleClient = new Client();

        //add loop for to Api
        $requests = [];
        foreach ($sellersValides as $sellersValide) {
            $baseUrl = $sellersValide['seller']['api']['baseUrl'];
            $apiKeyValue = $sellersValide['seller']['api']['apiKeyValue'];
            $login = $sellersValide['seller']['api']['login'];
            $password = $sellersValide['seller']['api']['password'];
            $offer = $sellersValide['offer'];
            foreach ($offer['offerProductTypes'] as $productType) {
                $productTypeName = $productType['productType']['name'];
                $productTypeMaxItems = $productType['maxItems'];
                if($productTypeName=='hotels'){
                $url = $baseUrl . '?' . http_build_query(['product' => $productTypeName]);
                $requests[] = new Request('POST', $url, [
                    'Content-Type' => 'application/json',
                    'api-key' => $apiKeyValue,
                    'Login' => $login,
                    'password' => $password,
                    'Access-Control-Allow-Origin' => '*',
                    'Accept' => '*/*'
                ], json_encode($searchHotel));
            }
        }
        }
        $results = [];

        $pool = new Pool($guzzleClient, $requests, [
            'concurrency' => 10, // Nombre maximal de requêtes en parallèle
            'fulfilled' => function ($response, $index) use (&$results) {
                $data = json_decode($response->getBody()->getContents(), true);
                $results[] = $data;
            },
            'rejected' => function ($reason, $index) {
                // Traiter l'erreur ici
                throw new Exception('Request failed: ' . $reason->getMessage());
            },
        ]);

        // Attendre que toutes les demandes soient terminées
        $pool->promise()->wait();

        return $this->json($results, 200);
    }
}
/*previousCode*/

//#[Route('/getApiHotel', name: 'ApiHotel_list', methods: ['GET','POST'])]
//    public function getApi_hotel(Request $request,SerializerInterface $serializer, SellerOfferRepository $sellerOfferRepository):Response
//    {
//        //$client = new Client();
//        $responseOfSellersValides = $this->sellerOfferController->getSellerOffers_lists($serializer, $sellerOfferRepository)->getContent();
//        $sellersValides = json_decode($responseOfSellersValides, true);
//
//        //get information about searchHotel from flutter
//        $searchHotel = json_decode($request->getContent(), true);
//        $checkInDate = $searchHotel['checkIn'];
//        $checkOutDate = $searchHotel['checkOut'];
//        $_city = $searchHotel['city'];
//        $occupancies = $searchHotel['occupancies'];
//
//
//
//        //add  loop for to Api
//        $dataArray = array();
//        foreach ($sellersValides as $sellersValide) {
//            $baseUrl = $sellersValide['seller']['api']['baseUrl'];
//            $apiKeyValue = $sellersValide['seller']['api']['apiKeyValue'];
//            $login = $sellersValide['seller']['api']['login'];
//            $password = $sellersValide['seller']['api']['password'];
//            $offer = $sellersValide['offer'];
//            foreach ($offer['offerProductTypes'] as $productType) {
//                $productTypeName = $productType['productType']['name'];
//                $productTypeMaxItems = $productType['maxItems'];
//                $url = $baseUrl . '?' . http_build_query(['product' => $productTypeName]);
//                //  $url = $baseUrl . '/' . $productTypeName;
//                // echo $url; // affiche "http://btob.3t.tn/getProducts?product=hotels"
//                //commented just for now
//                $response = $this->httpClient->request('POST', $url, [
//                    'headers' => [
//                        'Content-Type' => 'application/json',
//                        'api-key' => $apiKeyValue,
//                        'Login' => $login,
//                        'password' => $password,
//                        'Access-Control-Allow-Origin' => '*',
//                        'Accept' => '*/*'
//                    ],
//                    'json' => [
//                        "checkIn" => $checkInDate,
//                        "checkOut" => $checkOutDate,
//                        "city" => $_city,
//                        "hotelName" => "",
//                        "boards" => [],
//                        "rating" => [],
//                        "occupancies" => $occupancies,
//                        /*   for (int i = 1; i <= NbChambres2; i++)
//                               "$i" => [
//                       "adult" => i == 1 ? adult1 : i == 2 ? adult2 : adult3,
//                       "child" => [
//                           "value" => i == 1 ? enfant1 : i == 2 ? enfant2 : enfant3,
//                           "age" => _children
//                       ],*/
//                        // ],
//                        //  ],
//                        "channel" => "b2c",
//                        "language" => "fr_FR",
//                        "onlyAvailableHotels" => false,
//                        "marketId" => "1",
//                        "customerId" => "7",
//                        "backend" => 0,
//                        "filtreSearch" => []
//                    ],
//                ]);
//                if ($response->getStatusCode() == 200) {
//                    $data = json_decode($response->getContent(), true);
//                    $dataArray[] = $data;
//                } else {
//                    throw new Exception('Failed to fetch seller offers');
//                }
//
//            }
//        }
//        return $this->json($dataArray, 200);
//
//    }
//

