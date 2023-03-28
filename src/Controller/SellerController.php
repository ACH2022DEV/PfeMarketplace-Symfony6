<?php

namespace App\Controller;

use App\Entity\MarketSubscriptionRequest;
use App\Entity\Offer;
use App\Entity\Seller;
use App\Entity\SellerOffer;
use App\Entity\User;
use App\Events\SellerCreatedEvent;
use App\Form\SellerOfferType;
use App\Repository\SellerOfferRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Array_;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Helpers;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;


#[Route('/seller')]
class SellerController extends AbstractController
{
    private $em;

    public function __construct(  private Security $security,private ManagerRegistry $doctrine,SellerOfferRepository $sellerOfferRepository,SellerRepository $sellerRepository){
        $this->em=$doctrine;
        $this->sellerOfferRepository = $sellerOfferRepository;
        $this->sellerRepository = $sellerRepository;


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

    //en of add new page for edit
//add a controller for seller_Profile
    #[Route('/ViewProfile', name: 'app_seller_show', methods: ['GET'])]
    public function showProfile(Seller $seller): Response
    {
        return $this->render('seller/show.html.twig', [
            'seller' => $seller,
        ]);
    }

    //passez la commande
    #[Route('/Confirmation', name: 'app_seller_Confirmation', methods: ['GET'])]
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
    #[Route('/editProfile', name: 'app_seller_dashboard', methods: ['GET', 'POST'])]
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
    #[Route('/{id}', name: 'app_seller_delete', methods: ['POST'])]
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
    #[Route('/{id}/AddSellerOffer', name: 'Add_Seller_Offer', methods: ['POST'])]
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

    //afficher le panier d'un utilisateur
    #[Route('/MonPanier', name: 'Get_Seller_Panier', methods: ['GET'])]
    public function getPanier(Request $request, Security $security): Response
    {
        $user = $this->security->getUser();
        $userId = $user->getId();
        $session = $request->getSession();
        //$session->set('panier', 55);
        $seller = $this->em->getRepository(Seller::class)->findSellerByUserId($userId);
        return $this->render('seller/panier_Seller.html.twig', [
        'seller' => $seller,
            'session'=> $session,
            'userId'=>$userId,

        ]);
    }
    //fin panier

    //supprimer un offre d'un panier()sellerOffers
    #[Route('/{id}/RemoveOffer', name: 'delete_sellerOffer', methods: ['POST'])]
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

   #[Route('/ValiderCommande', name: 'app_seller_validationPanier', methods: ['GET', 'POST'])]
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
    public function cart(Request $request,Security $security): Response
    {
        $user = $this->security->getUser();
        $userId = $user->getId();
        var_dump($userId);
        $seller = $this->em->getRepository(Seller::class)->findSellerByUserId($userId);

        $seller = $this->em->getRepository(Seller::class)->findSellerByUserId($userId);
        var_dump($seller->getId());


       //var_dump($seller->getId());

        $session = $request->getSession();

        $panier = $request->getSession()->get('panier');
        //  $session = $request->getSession();

        $formBuilder = $this->createFormBuilder();
        foreach ($panier as $offer) {
            $formBuilder->add('startDate_'.$offer->getId(), DateType::class, [
                'label' => $offer->getName(),
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'datepicker'],
                'data' => new \DateTime(),
            ]);
        }

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            foreach ($panier as $offer) {
                $sellerOffer = new SellerOffer();
                $sellerOffer->setOffer($offer);
                $entityManager = $this->em->getManager();



               /* $sellerId=24;
                $seller = $this->em->getRepository(Seller::class)->find($sellerId);*/
                $sellerOffer->setSeller($seller);
                $sellerOffer->setCreationDate(new \DateTime());
                $sellerOffer->setStartDate($form->get('startDate_'.$offer->getId())->getData());

                $entityManager->persist($sellerOffer);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_offer_index');
        }

        return $this->render('seller/validation_Panier.html.twig', [
            'panier' => $panier,
            'session'=> $session,
            'form' => $form->createView(),
        ]);
    }

}
