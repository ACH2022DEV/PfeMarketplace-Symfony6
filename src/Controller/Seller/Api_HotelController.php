<?php

namespace App\Controller\Seller;


use App\Controller\Shared\SellerOfferController;
use App\Repository\SellerOfferRepository;
use App\Service\Helpers;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use GuzzleHttp\Client;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//use Symfony\Component\HttpFoundation\Request;
//use http\Client;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
//use GuzzleHttp\Promise\Promise;
//use React\Promise\Promise;
use GuzzleHttp\Promise;






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
    #[Route('/getApiHotels', name: 'ApiHotel_list', methods: ['GET','POST'])]
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
                  //  $url=$baseUrl;
                    $requests[] = new Request('POST', $url, [
                        'Content-Type' => 'application/json',
                        'api-key' => $apiKeyValue,
                        'Login' => $login,
                        'password' => $password,
                        'Access-Control-Allow-Origin' => '*',
                        'Accept' => '*/*'
                    ], json_encode($searchHotel));
//                    $requests[] = new Request('GET', $url, [
//                        'Content-Type' => 'application/json',
//                        'Access-Control-Allow-Origin' => '*',
//                        'Accept' => '*/*'
//                    ], json_encode(null));
                }
            }
        }
        //1ème méthode
        //$results = [];
        $hotels = [];
        $pool = new Pool($guzzleClient, $requests, [
            'concurrency' => 10, // Nombre maximal de requêtes en parallèle
            'fulfilled' => function ($response, $index) use (&$hotels,$sellersValides) {

               $seller = $sellersValides[$index];
               // dd($seller);
                $data = json_decode($response->getBody()->getContents(), true);
              //  $hotels[] = $data['response'];
               // dd($hotels);
                //premiére methode
               foreach ($data['response'] as $dataIndex) {
                    $hotel = $dataIndex;
                    // Vérifier si l'hôtel existe déjà dans le tableau des hôtels
                    if (isset($hotels[$hotel['hotelName']])) {
                      //  $hotels[$hotel['hotelName']][] =  $seller['seller'];
                       // $hotels[$hotel['hotelName']]['seller'][] = ['seller'=>$seller['seller'],'hotelSeller'=>$hotel['lowPrice']];
                        $hotels[$hotel['hotelName']]['sellers'][] = [
                            'sellerData' => $seller['seller'],
                            'PrixSeller' => $hotel['lowPrice'],
                            'detailsLink' => $hotel['detailsLink']
                        ];

                        // Ajouter le vendeur à l'hôtel existant
                    } else {
                      //  $hotels[$hotel['hotelName']] = ['hotel'=>$hotel ,'seller'=>['seller'=>$seller['seller'],'hotelSeller'=>$hotel['lowPrice']]];
                        $hotels[$hotel['hotelName']] = [
                            'hotel' => $hotel,
                            'sellers' => [
                                [
                                    'sellerData' => $seller['seller'],
                                    'PrixSeller' => $hotel['lowPrice'],
                                    'detailsLink' => $hotel['detailsLink']
                                ]
                            ]
                        ];// Créer un nouvel hôtel et ajouter le vendeur
                    }




                }
               //$hotels[$hotel['hotelName']] = array_values($hotels[$hotel['hotelName']]);
              //  $hotels = array_values($hotels);
                //$hotels = array_values($hotels);
//                $hotels = array_values($hotels);

                //2éme méthode

              /*  foreach ($data['response'] as $dataIndex) {
                    $hotel = $dataIndex['hotelName'];

                    // Vérifier si l'hôtel existe déjà dans le tableau des hôtels
                    $existingHotel = array_filter($hotels, function ($value) use ($hotel) {
                        return $value['hotel']['hotelName'] === $hotel;
                    });

                    if (!empty($existingHotel)) {
                        // Ajouter le vendeur à l'hôtel existant
                        $existingHotel[key($existingHotel)]['sellers'][] = $seller['seller'];
                    } else {
                        // Créer un nouvel hôtel et ajouter le vendeur
                        $hotels[] = [
                            'hotel' => $dataIndex,
                            'sellers' => $seller['seller']
                        ];
                    }
                }*/

                //end methode
                    /*foreach ($data['response'] as $dataindex){
                      //  dd($dataindex);
                      //  $hotel =$dataindex['hotelName'];
                        if (in_array($dataindex, $data['response'])) {
                            $hotels[] = ['seller' => $seller['seller']];// Ajouter le vendeur aux résultats
                        } else {
                            $hotels[] =  ['seller' => $seller['seller'] ,'hotel'=> $dataindex];
                           // $hotels[] = $dataindex; // Ajouter l'hôtel au tableau des hôtels
                        }*/
                      //  $results[] = ['seller' => $seller['seller'], 'data' =>['hotel'=> $dataindex]];
                   //}
                // $results[] = ['seller' => $seller['seller'], 'data' => $data];
            //  $results[] = ['seller' => $seller['seller'], 'data' => $data['response']];
           //    if()
              //  $results[] = ['seller' => $seller['seller']];

            },
            'rejected' => function ($reason, $index) {
                // Traiter l'erreur ici
                throw new Exception('Request failed: ' . $reason->getMessage());
            },
        ]);

        // Attendre que toutes les demandes soient terminées
        $pool->promise()->wait();
        //$guzzleClient->

        return $this->json($hotels, 200);
    }
    //end 1ème méthode
    //2ème méthode
    // Créer un tableau de promesses pour chaque requête
 /*      $promises = [];
        foreach ($requests as $request) {
            $promises[] = $guzzleClient->sendAsync($request);
        }

        // Attendre que toutes les promesses soient résolues
        $results = [];
        $resultsPromise = Promise\all($promises)->then(
            function ($responses) use (&$results) {
                foreach ($responses as $response) {
                    $data = json_decode($response->getBody()->getContents(), true);
                    $results[] = $data;
                }
            }
        );
        $resultsPromise->wait();

        return $this->json($results, 200);
    }*/
    //fin 2ème méthode

/*previousCode*/

//#[Route('/getApiHotel', name: 'ApiHotel_list', methods: ['GET','POST'])]
//    public function getApi_hotel2(Request $request,SerializerInterface $serializer, SellerOfferRepository $sellerOfferRepository):Response
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

}
