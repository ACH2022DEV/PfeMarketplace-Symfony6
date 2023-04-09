<?php

namespace App\Controller;

use App\Entity\MarketSubscriptionRequest;
use App\Entity\Offer;
use App\Entity\Seller;
use App\Entity\SellerOffer;
use App\Entity\User;
use App\Events\SellerCreatedEvent;
use App\Form\SellerOfferType;
use App\Repository\OfferRepository;
use App\Repository\SellerOfferRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use http\Exception;
use phpDocumentor\Reflection\Types\Array_;
use PhpParser\Node\Expr\Cast\Double;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Form\SellerType;
use App\Repository\SellerRepository;
use App\Repository\UserRepository;
use App\Service\Mailer;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Helpers;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;


//#[Route('/seller')]
class SellerController extends AbstractController
{
    private $em;

    public function __construct(  private Security $security,private ManagerRegistry $doctrine,SellerOfferRepository $sellerOfferRepository,SellerRepository $sellerRepository){
        $this->em=$doctrine;
        $this->sellerOfferRepository = $sellerOfferRepository;
        $this->sellerRepository = $sellerRepository;


    }
    #[Route('/seller/', name: 'app_seller_index', methods: ['GET'])]
    public function index(SellerRepository $sellerRepository): Response
    {
        return $this->render('seller/index.html.twig', [
            'sellers' => $sellerRepository->findAll(),
        ]);
    }
    /*#[Route('/getAllSeller', name: 'Seller_list', methods: ['GET'])]
    public function getAllSellers(SerializerInterface $serializer,SellerRepository $sellerRepository): Response
    {
        $List_Seller=$sellerRepository->findAll();
         $serializedSeller = $serializer->serialize($List_Seller, 'json', ['groups' => 'Seller']);
        $data = json_decode($serializedSeller, true);
        return $this->json($data,200);
    }*/
    #[Route('/getAllSeller', name: 'Seller_list', methods: ['GET'])]
    public function getAllSellers(SerializerInterface $serializer, SellerRepository $sellerRepository): Response
    {
        $List_Seller = $sellerRepository->findAll();

        // Option de normalisation pour convertir les objets en tableaux associatifs
        $normalizedSeller = $serializer->normalize($List_Seller, null, ['groups' => 'Seller']);

        // Conversion du tableau associatif en JSON
        $serializedSeller = $serializer->serialize($normalizedSeller, 'json');

        // Décode le JSON en tant qu'objet ou tableau PHP
        $data = json_decode($serializedSeller, true);

        // Retourne la réponse JSON
        return $this->json($data, 200);
    }

    #[Route('/seller/new/{idM?null}', name: 'app_seller_new', methods: ['GET', 'POST'])]
    public function new(Request $request,
                        SellerRepository $sellerRepository,
                        UserRepository $userRepository,
                        Helpers $helpers,
                        EventDispatcherInterface $dispatcher,
                        UserPasswordHasherInterface $passwordHasher,
                        MarketSubscriptionRequest $idM,

    ): Response
    {

        if(!is_null($idM) && strcmp($idM->getStatus(),"validated")==0){
            return $this->redirectToRoute('app_market_subscription_request_index');
        }
        $user = new User();
        $seller = new Seller();
        $seller->setUser($user);
        if($idM !=null){
            $marketSubscriptionRequest=$idM;
            $password = $helpers->generateRandomPassword();
            $user->setEmail($marketSubscriptionRequest->getEmail());
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $password
                )
            );
            $user->setDisplayName($marketSubscriptionRequest->getName());
            $name = str_replace(' ', '_', $marketSubscriptionRequest->getName());
            $user->setUsername($name);
            $user->setRoles((array)'ROLE_SELLER');

            $seller->setUser($user);
            $seller->setName($marketSubscriptionRequest->getName());
            $seller->setWebsite($marketSubscriptionRequest->getWebsite());
            $seller->setAddress($marketSubscriptionRequest->getAddress());
            $seller->setCity($marketSubscriptionRequest->getCity());
            //$seller->setApi();
        }
        $form = $this->createForm(SellerType::class, $seller);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $marketSubscriptionRequest->setStatus('validated');
            $userRepository->add($user, true);
            $sellerRepository->save($seller, true);


          //  $onCreateSellerEvent = new SellerCreatedEvent($seller, $password);
          //  $dispatcher->dispatch($onCreateSellerEvent);

            return $this->redirectToRoute('app_seller_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'new.html.twig';

        return $this->renderForm('seller/'.$template, [
            'seller' => $seller,
            'form' => $form,
        ],
            new Response(
                null,
                $form->isSubmitted() && !$form->isValid() ? 422 : 200,
            ));
    }

    #[Route('/seller/{id}', name: 'app_seller_show', methods: ['GET'])]
    public function show(Seller $seller): Response
    {
        return $this->render('seller/show.html.twig', [
            'seller' => $seller,
        ]);
    }

    //add an new page for Edit_seller

   /* #[Route('/editProfileU', name: 'app_seller_dashboardEdit', methods: ['GET', 'POST'])]
    public function editProfile(): Response
    {
       // $seller = $this->getUser();

        return $this->render('seller/edit.html.twig', [
          //  'seller' => $seller,
           // 'form' => $form
        ]);
    }*/

    //en of add new page for edit
//add a controller for seller_Profile and sellerBooking_history
    #[Route('/seller/View_Booking_History', name: 'app_seller_show_Booking_History', methods: ['GET'])]
    public function showBookingHistory(Request $request): Response
    {
        $user = $this->security->getUser();
        $userId = $user->getId();
        $session = $request->getSession();
        $seller = $this->em->getRepository(Seller::class)->findSellerByUserId($userId);

        //pagination
        $page = $request->query->getInt('page', 1);
        $limit = 10; // nombre d'éléments par page
        $offset = ($page - 1) * $limit;
        $sellerOffers=$seller->getSellerOffers();

      //  $sellerOffers = // Récupérez vos offres de vendeurs à partir de votre base de données

        $totalOffers = count($sellerOffers);

        $totalPages = ceil($totalOffers / $limit);
        //fin de pagination
        return $this->render('seller/booking_seller_History.html.twig', [
            'seller' => $seller,

          //  'sellerOffers' => to($sellerOffers->getValues(), $offset, $limit),
            //'sellerOffers' => array_slice($sellerOffers->getValues(), $offset, $limit),
            //'sellerOffers' => array_slice($sellerOffers->toArray(), $offset, $limit),
            //'sellerOffers' => array_slice($sellerOffers->slice($offset), $limit),
              'sellerOffers' => array_slice($sellerOffers->getValues(),$offset, $limit),
            'page' => $page,
            'limit' => $limit,
            'totalPages' => $totalPages,
        ]);
    }
    #[Route('/seller/ViewProfile', name: 'app_seller_show', methods: ['GET'])]
    public function showProfile(Seller $seller): Response
    {
        return $this->render('seller/show.html.twig', [
            'seller' => $seller,
        ]);
    }

    //passez la commande
    #[Route('/seller/Confirmation', name: 'app_seller_Confirmation', methods: ['GET'])]
    public function passezCommande(Request $request, Security $security): Response
    {
        $user = $this->security->getUser();
        $userId = $user->getId();
        $session = $request->getSession();
        $seller = $this->em->getRepository(Seller::class)->findSellerByUserId($userId);
        return $this->render('seller/commandeConfirmation.html.twig', [
            'seller' => $seller,
        ]);
    }


    //


//
//add images to seller
   /* #[Route('/{id}/edit', name: 'app_seller_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Seller $seller, SellerRepository $sellerRepository): Response
    {
        $form = $this->createForm(SellerType::class, $seller);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sellerRepository->save($seller, true);

            return $this->redirectToRoute('app_seller_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('seller/edit.html.twig', [
            'seller' => $seller,
            'form' => $form,
        ]);
    }*/


    //add a uploader images:
    #[Route('/seller/editProfile', name: 'app_seller_dashboard', methods: ['GET', 'POST'])]
    public function edit_Profil_SellerAndImages(Request $request, SellerRepository $sellerRepository, SluggerInterface $slugger): Response
    {
        $session = $request->getSession();
        if ($session->isStarted()) {
            $user = $this->security->getUser();
            $userId = $user->getId();
            $seller = $this->em->getRepository(Seller::class)
                ->findSellerByUserId($userId);
        }

        $form = $this->createForm(SellerType::class, $seller);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('brochure')->getData();

            // check if a new file has been uploaded
            if ($brochureFile) {
                // delete the old file
                $oldFilename = $seller->getBrochureFilename();
                if ($oldFilename) {
                    $oldFilePath = $this->getParameter('brochures_directory') . '/' . $oldFilename;
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }

                // save the new file
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();

                try {
                    $brochureFile->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // handle exception
                }

                $seller->setBrochureFilename($newFilename);
            }

            $sellerRepository->save($seller, true);

            return $this->redirectToRoute('app_offer_seller', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('seller/edit.html.twig', [
            'seller' => $seller,
            'userId' => $userId,
            'form' => $form
        ]);
    }

    //end of add uploader images

//end images
    #[Route('/seller/{id}', name: 'app_seller_delete', methods: ['POST'])]
    public function delete(Request $request, Seller $seller, SellerRepository $sellerRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$seller->getId(), $request->request->get('_token'))) {
            $sellerRepository->remove($seller, true);
        }

        return $this->redirectToRoute('app_seller_index', [], Response::HTTP_SEE_OTHER);
    }


// ajouterAuPanier
  /*  #[Route('/{id}/AddSellerOffer1', name: 'Add_Seller_Offer2', methods: ['POST'])]
    public function ajouterAuPanier1(Request $request, Security $security): Response
    {
        $user = $this->security->getUser();
        $userId = $user->getId();
        $session = $request->getSession();
        if (!$user) {
            // L'utilisateur n'est pas authentifié, redirection vers la page de connexion
            return $this->render('shared/login/login_Seller.html.twig');
        }
        return $this->render('seller/show.html.twig', [
          //  'seller' => $seller,
        ]);
    }*/
    #[Route('/seller/{id}/AddSellerOffer', name: 'Add_Seller_Offer', methods: ['POST'])]
    public function ajouterAuPanier(Request $request, Security $security): Response
    {
        //$data = json_decode($request->getContent(), true);
        $user = $this->security->getUser();
        $userId = $user->getId();
        $session = $request->getSession();
        $panier = $session->get('panier', new ArrayCollection());
        $seller = $this->em->getRepository(Seller::class)->findSellerByUserId($userId);
        $id = $request->attributes->get('id');
        $offer = $this->em->getRepository(Offer::class)->find($id);

      // $StartDate=new DateTime();

        $isAlreadyInCart = false;
        foreach ($panier as $item) {
            if ($item->getId() === $offer->getId()) {
                $isAlreadyInCart = true;
                break;
            }
        }

        if ($isAlreadyInCart) {
            return $this->redirectToRoute('app_offer_seller', [], Response::HTTP_SEE_OTHER);
        } else {
            $panier->add($offer);
            $session->set('panier', $panier);
        }


        return $this->render('seller/panier_Seller.html.twig', [
              'seller' => $seller,
            'session'=> $session,
            'userId'=>$userId,
        ]);

    }

    //fin ajouterAuPanier
  /*  public function getOffersTotalPrice( Request $request,OfferRepository $offerRepository): array
    {
        $session = $request->getSession();
        $offersTotalPrice = [];
        $panier = $session->get('panier');
        foreach ($panier as $offerId) {
            $offer = $offerRepository->find($offerId);
            $offerProductTypes = $offer->getOfferProductTypes();
            $totalPrice = 0.0;
            foreach ($offerProductTypes as $offerProductType) {
                $totalPrice += $offerProductType->getPrice();
            }
            $offersTotalPrice[] = ['id' => $offerId, 'totalPrice' => $totalPrice];
        }
        return $offersTotalPrice;
    }*/
    //afficher le panier d'un utilisateur
    #[Route('/seller/MonPanier', name: 'Get_Seller_Panier', methods: ['GET'])]
    public function getPanier(Request $request, Security $security): Response
    {
        $user = $this->security->getUser();
        $userId = $user->getId();
        $session = $request->getSession();
        //$session->set('panier', 55);
        $seller = $this->em->getRepository(Seller::class)->findSellerByUserId($userId);
        $totalPrice = 0.0;
        $panier = $session->get('panier');
        if($panier){
        foreach ($panier as $offer) {
            $offerId = $offer->getId();
            $offerRepo = $this->em->getRepository(Offer::class);
            $offer = $offerRepo->find($offerId);
            $offerProductTypes = $offer->getOfferProductTypes();
            $offerPriceTotal = 0.0;
            foreach ($offerProductTypes as $offerProductType) {
                $offerPriceTotal += $offerProductType->getPrice();
            }
            $totalPrice += $offerPriceTotal;
        }
        }
//        foreach ($panier as $offer) {
//            $offerId = $this->em->getRepository(Offer::class)->find($offer);
//            $totalPrice += $this->getTotalPrice($offerId);
//        }


        //add a methode
       //fin méthode
        $panier = $session->get('panier');
//        foreach ($panier as $offerId) {
//            $offer = $this->em->getRepository(Offer::class)->find($offerId);
//            // Obtenez la collection d'OfferProductType associée à l'entité Offer.
//            $offerProductTypes = $offer->getOfferProductTypes();
//            //Calculer la somme
//            $totalPrice = 0.0;
//            foreach ($offerProductTypes as $offerProductType) {
//                $totalPrice += $offerProductType->getPrice();
//            }

            // Utilisez la méthode map() pour obtenir une collection de prix pour chaque OfferProductType.
//            $prices = $offerProductTypes->map(function($offerProductType) {
//                return $offerProductType->getPrice();
//            });
            // Utilisez la méthode sum() pour calculer la somme des prix.
           // $totalPrice = $prices->map();

            // Faites quelque chose avec le prix total...
      //  }

        //end of total
        return $this->render('seller/panier_Seller.html.twig', [
        'seller' => $seller,
            'session'=> $session,
            'userId'=>$userId,
            'totalPrice'=> $totalPrice

        ]);
    }
    //fin panier

    //supprimer un offre d'un panier()sellerOffers
    #[Route('/seller/{id}/RemoveOffer', name: 'delete_sellerOffer', methods: ['POST'])]
    public function removeOfferFromCart(Request $request, Security $security):Response
    {
        $user = $this->security->getUser();
        $userId = $user->getId();
        $session = $request->getSession();
        $id = $request->attributes->get('id');
        $offer = $this->em->getRepository(Offer::class)->find($id);
        $panier = $session->get('panier');
        foreach ($panier as $key => $item) {
            if ($item->getId() === $offer->getId()) {
                $panier->remove($key);
                break;
            }
        }

        $session->set('panier', $panier);


        return $this->redirectToRoute('Get_Seller_Panier', [], Response::HTTP_SEE_OTHER);
    }

    //
   /* #[Route('/{id}/RemoveOffer', name: 'delete_sellerOffer', methods: ['POST'])]
    public function removeOfferFromCart(Request $request, Security $security): Response
    {
        $user = $security->getUser();
        $userId = $user->getId();
        $session = $request->getSession();
        $id = $request->attributes->get('id');
        $offer = $this->em->getRepository(Offer::class)->find($id);
        $panier = $session->get('panier') ?? new ArrayCollection();

        $indexToRemove = null;
        foreach ($panier as $index => $item) {
            if ($item === $offer) {
                $indexToRemove = $index;
                break;
            }
        }

        if ($indexToRemove !== null) {
            $panier->removeElement($offer);
           // $panier->removeElement()
            $session->set('panier', $panier);
        }

        return $this->redirectToRoute('Get_Seller_Panier', [], Response::HTTP_SEE_OTHER);
    }*/
    //Validation d'un commande

   #[Route('/seller/ValiderCommande', name: 'app_seller_validationPanier', methods: ['GET', 'POST'])]
    public function validerCommande(Request $request,Security $security,SellerOfferRepository $sellerOfferRepository ): Response
    {

       /* //ajouter le formulaire de sellersOffers
        $sellerOffer = new SellerOffer();
        $form = $this->createForm(SellerOfferType::class, $sellerOffer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sellerOfferRepository->save($sellerOffer, true);

            return $this->redirectToRoute('app_seller_offer_index', [], Response::HTTP_SEE_OTHER);
        }

        //fin sellersOffers
        $user = $this->security->getUser();
        $userId = $user->getId();
        $session = $request->getSession();
        $seller = $this->em->getRepository(Seller::class)->findSellerByUserId($userId);*/
        return $this->render('seller/show.html.twig', [
           /* 'seller' => $seller,
            'session'=> $session,
            'userId'=>$userId,
            'form' => $form,
            'seller_offer' => $sellerOffer,*/
        ]);
    }
    //fin Validation d'un commande
    #[Route('/NewPanier', name: 'app_seller_NewPanier', methods: ['POST','GET'])]
    public function cart(Request $request, Security $security): Response
    {
        $user = $security->getUser();
        $userId = $user->getId();
        //var_dump($userId);

        $seller = $this->em->getRepository(Seller::class)->findSellerByUserId($userId);

        if (!$seller) {
            throw new \Exception("No seller found for user with ID $userId");
        }

       // var_dump($seller->getId());

        $session = $request->getSession();

        $panier = $session->get('panier');

        $formBuilder = $this->createFormBuilder();

        foreach ($panier as $offer) {
            //pour ajouter just le jour sans minutes
          /*  $formBuilder>add('startDate_' . $offer->getId(), DateType::class, [
                'label' => $offer->getName(),
                'widget' => 'single_text',
                'html5' => true,
                'attr' => ['class' => 'datepicker'],
                'data' => new \DateTime(),
            ]);*/
            $formBuilder->add('startDate_' . $offer->getId(), DateTimeType::class, [
                'label' => $offer->getName(),
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'class' => 'datetimepicker',
                    'type' => 'datetime-local',
                    'step' => '1'
                ],
                'data' => new \DateTime(),
            ]);
        }

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->em->getManager();

            foreach ($panier as $offer) {
                $offerId = $this->em->getRepository(Offer::class)->find($offer);
                $sellerOffer = new SellerOffer();
                $sellerOffer->setOffer($offerId);
                $sellerOffer->setSeller($seller);
                $sellerOffer->setCreationDate(new \DateTime());
                $sellerOffer->setStartDate($form->get('startDate_' . $offer->getId())->getData());

                $entityManager->persist($sellerOffer);

              //  $entityManager->remove($offer);

            }
            $entityManager->flush();

            return $this->redirectToRoute('app_seller_Confirmation');
        }

        return $this->render('seller/validation_Panier.html.twig', [
            'panier' => $panier,
            'session'=> $session,
            'form' => $form->createView(),
        ]);
    }

    private function getTotalPrice($offer)
    {
        $offerProductTypes = $offer->getOfferProductTypes();
        $prices = $offerProductTypes->map(function($offerProductType) {
            return $offerProductType->getPrice();
        });
        $totalPrice = $prices->sum();
        return $totalPrice;
    }
//get image of seller
    #[Route('/getImages', name: 'app_seller_Images', methods: ['GET'])]
    public function getImage(Request $request): BinaryFileResponse
    {
        // file name from database
        $filePath = $this->getParameter('brochures_directory').'/'.$request->query->get('filename');
        // check if there is filepath or not
        $path = (file_exists($filePath)) ? $filePath : $this->getParameter('brochures_directory2').'/error/avatar.png';
        // Create a response object with the file data
        $response = new BinaryFileResponse($path);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE);
        return $response;
    }

    /* #[Route('/getImages', name: 'app_seller_Images', methods: ['GET'])]
     public function getImage(Request $request): BinaryFileResponse
     {
         //$filename = $request->query->get('filename');
         $filename = $this->getParameter('brochures_directory').'/brochures/'.$request->query->get('filename');
         // search for the seller entity with the matching brochureFilename
         $seller = $this->em->getRepository(Seller::class)->findOneBy(['brochureFilename' => $filename]);

         if (!$seller) {
           //  return $this->json(['error' => 'Seller not found'], 404);
             throw new \Exception('Seller not found', 404);
         }

         // get the full path of the image file
         //$filePath = $this->getParameter('brochures_directory').'/brochures/'.$seller->getBrochureFilename();
         $filePath = (file_exists($seller)) ? $filename : $this->getParameter('brochures_directory').'/error/avatar.png';
         // check if the file exists
         if (!file_exists($filePath)) {
             $filePath = $this->getParameter('brochures_directory').'/error/avatar.png';
         }

         // create a response object with the file data
        // return $this->json(new BinaryFileResponse($filePath));
         $response = new BinaryFileResponse($filePath);
         $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE);
         return $response;

     }*/
//end images

   /* #[Route('/getImages', name: 'app_seller_Images', methods: ['GET'])]
    public function getImage(Request $request): JsonResponse
    {
        $filename = $request->query->get('filename');

        // search for the seller entity with the matching brochureFilename
        $seller = $this->getDoctrine()->getRepository(Seller::class)->findOneBy(['brochureFilename' => $filename]);

        if (!$seller) {
            return $this->json(['error' => 'Seller not found'], 404);
        }

        // get the full path of the image file
        $filePath = $this->getParameter('brochures_directory').'/brochures/'.$seller->getBrochureFilename();

        // check if the file exists
        if (!file_exists($filePath)) {
            $filePath = $this->getParameter('brochures_directory').'/error/avatar.png';
        }

        // create a response object with the file data
        return $this->json(new BinaryFileResponse($filePath));
    }*/

}
