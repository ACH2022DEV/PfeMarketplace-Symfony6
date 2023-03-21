<?php

namespace App\Controller;

use App\Entity\MarketSubscriptionRequest;
use App\Entity\Offer;
use App\Entity\Seller;
use App\Entity\SellerOffer;
use App\Entity\User;
use App\Events\SellerCreatedEvent;
use App\Repository\SellerOfferRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Form\SellerType;
use App\Repository\SellerRepository;
use App\Repository\UserRepository;
use App\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Helpers;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;


#[Route('/seller')]
class SellerController extends AbstractController
{
    private $em;

    public function __construct(  private Security $security,private ManagerRegistry $doctrine,SellerOfferRepository $sellerOfferRepository){
        $this->em=$doctrine;
        $this->sellerOfferRepository = $sellerOfferRepository;

    }
    #[Route('/', name: 'app_seller_index', methods: ['GET'])]
    public function index(SellerRepository $sellerRepository): Response
    {
        return $this->render('seller/index.html.twig', [
            'sellers' => $sellerRepository->findAll(),
        ]);
    }

    #[Route('/new/{idM?null}', name: 'app_seller_new', methods: ['GET', 'POST'])]
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

    #[Route('/{id}', name: 'app_seller_show', methods: ['GET'])]
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
    #[Route('/editProfile', name: 'app_seller_dashboard', methods: ['GET', 'POST'])]
    public function edit_Profil_Seller(Request $request, SellerRepository $sellerRepository): Response
    {
        //, Seller $seller, SellerRepository $sellerRepository
     $session = $request->getSession();
        if ( $session->isStarted()) {
            $user = $this->security->getUser();
            $userId = $user->getId();
           // $userId = $request->getSession()->get('id');
            $seller = $this->em->getRepository(Seller::class)
                ->findSellerByUserId($userId);
        }

        $form = $this->createForm(SellerType::class, $seller);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sellerRepository->save($seller, true);

            return $this->redirectToRoute('app_offer_seller', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('seller/edit.html.twig', [
           'seller' => $seller,
            'userId' => $userId,
            'form' => $form
        ]);
    }
    //en of add new page for edit
//add a controller for seller_Profile
    #[Route('/ViewProfile', name: 'app_seller_show', methods: ['GET'])]
    public function showProfile(Seller $seller): Response
    {
        return $this->render('seller/show.html.twig', [
            'seller' => $seller,
        ]);
    }


//
    #[Route('/{id}/edit', name: 'app_seller_edit', methods: ['GET', 'POST'])]
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
    }

    #[Route('/{id}', name: 'app_seller_delete', methods: ['POST'])]
    public function delete(Request $request, Seller $seller, SellerRepository $sellerRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$seller->getId(), $request->request->get('_token'))) {
            $sellerRepository->remove($seller, true);
        }

        return $this->redirectToRoute('app_seller_index', [], Response::HTTP_SEE_OTHER);
    }


// ajouterAuPanier
    #[Route('/{id}/AddSellerOffer1', name: 'Add_Seller_Offer2', methods: ['POST'])]
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
    }
    #[Route('/{id}/AddSellerOffer', name: 'Add_Seller_Offer', methods: ['POST'])]
    public function ajouterAuPanier(Request $request, Security $security): JsonResponse
    {
        //$data = json_decode($request->getContent(), true);
        $user = $this->security->getUser();
        $userId = $user->getId();
        $session = $request->getSession();


        $seller = $this->em->getRepository(Seller::class)->findSellerByUserId($userId);
        $sellerOffer = new SellerOffer();
        $sellerOffer->setSeller($seller);
        $sellerOffer->setCreationDate(new DateTime());
        $sellerOffer->setStartDate(new DateTime());
        $id = $request->attributes->get('id');

       // var_dump($id); // affiche la valeur de l'ID dans la console ou dans la page web
        // $id = $request->request->get('id');
        //$id=64;
        $offer = $this->em->getRepository(Offer::class)->find($id);
        $existingSellerOffer = $this->sellerOfferRepository->findExistingSellerOffer($seller->getId(), $offer->getId());
        if ($existingSellerOffer) {
            return new JsonResponse([
                'status' => false,
                'message' => 'L\'offre existe déjà dans le panier de ce vendeur.',
            ]);
        }else{
            $sellerOffer->setOffer($offer);
        }



        $entityManager = $this->em->getManager();
        $entityManager->persist($sellerOffer);
        $entityManager->flush();

        return new JsonResponse([
            'status' => true,
            'message' => 'Vous avez ajouté les offres à votre panier',
        ]);
    }

    //fin ajouterAuPanier

    //afficher le panier d'un utilisateur
    #[Route('/MonPanier', name: 'Get_Seller_Panier', methods: ['GET'])]
    public function getPanier(Request $request, Security $security): Response
    {
        $user = $this->security->getUser();
        $userId = $user->getId();
        $session = $request->getSession();
        $seller = $this->em->getRepository(Seller::class)->findSellerByUserId($userId);
        return $this->render('seller/panier_Seller.html.twig', [
        'seller' => $seller
        ]);
    }
    //fin panier

    //supprimer un offre d'un panier()sellerOffers
    #[Route('/{id}/RemoveOffer', name: 'delete_sellerOffer', methods: ['POST'])]
    public function removeOfferFromCart(Request $request, SellerOffer $sellerOffer, SellerOfferRepository $sellerOfferRepository):Response
    {
       /* $user = $this->security->getUser();
        $userId = $user->getId();
        $session = $request->getSession();
        $seller = $this->em->getRepository(Seller::class)->findSellerByUserId($userId);
        //$sellerOffers=$seller->getSellerOffers();
        $id = $request->attributes->get('id');
        $offer = $this->em->getRepository(SellerOffer::class)->find($id);
        if ($this->isCsrfTokenValid('delete'.$offer->getId(), $request->request->get('_token'))) {
            $sellerOfferRepository->remove($offer, true);
        }
        if ($sellerOffers->contains($offer)) {
            $sellerOffers->removeElement($offer);
            $entityManager = $this->em->getManager();
            $entityManager->flush();
        }*/
        $id = $request->attributes->get('id');
        $offerSeller = $this->em->getRepository(SellerOffer::class)->find($id);
        $entityManager = $this->em->getManager();
        $entityManager->remove($offerSeller);
        $entityManager->flush();

      // return $this->redirectToRoute('Get_Seller_Panier');
        return $this->redirectToRoute('Get_Seller_Panier', [], Response::HTTP_SEE_OTHER);
    }
  /*  #[Route('/{id}/RemoveOffer', name: 'delete_sellerOffer', methods: ['POST'])]
    public function removeOfferFromCart(Request $request, $id)
    {
        $user = $this->security->getUser();
        if (!$user) {
            // user is not authenticated
            throw new \Exception('User is not authenticated.');
        }
        $userId = $user->getId();
        $session = $request->getSession();
        $seller = $this->em->getRepository(Seller::class)->findSellerByUserId($userId);
        if (!$seller) {
            // seller not found for this user
            throw new \Exception('Seller not found for this user.');
        }
        $sellerOffers = $seller->getSellerOffers();
        if (!$sellerOffers) {
            // seller offers collection not found
            throw new \Exception('Seller offers collection not found.');
        }
        $offer = $this->em->getRepository(SellerOffer::class)->find($id);
        if (!$offer) {
            // offer not found in database
            throw new \Exception('Offer not found in database.');
        }
        if (!$sellerOffers->contains($offer)) {
            // offer not found in seller offers collection
            throw new \Exception('Offer not found in seller offers collection.');
        }
        $sellerOffers->removeElement($offer);
        $entityManager = $this->em->getManager();
        $entityManager->flush();

        return $this->redirectToRoute('Get_Seller_Panier');
    }*/


    //
}
