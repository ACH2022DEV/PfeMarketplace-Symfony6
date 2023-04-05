<?php

namespace App\Controller\Shared;

use App\Entity\Offer;
use App\Entity\Seller;
use App\Entity\SellerOffer;
use App\Form\OfferType;
use App\Repository\OfferRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;


#[Route('/offer')]
class OfferController extends AbstractController
{
    private $manager;
    private $offer;
    private $em;
    #[Route('/', name: 'app_offer_index', methods: ['GET'])]
    public function index(OfferRepository $offerRepository): Response
    {
        return $this->render('offer/index.html.twig', [
            'offers' => $offerRepository->findAll(),
        ]);
    }
    //add an offer controller
    #[Route('/getAllOffers', name: 'app_offer_seller', methods: ['GET'])]
    public function getOffer(OfferRepository $offerRepository): Response
    {
        return $this->render('seller/dashboard/offerList.html.twig', [
            'offer' => $offerRepository->findAll(),
        ]);
    }
    //add home Page
    #[Route('/Home', name: 'app_Home_seller', methods: ['GET'])]
    public function getPageHome(): Response
    {
        return $this->render('seller/dashboard/home_seller.html.twig', [
            //'offer' => $offerRepository->findAll(),
        ]);
    }

    //end home page
    #[Route('/GetAllOfferse', name: 'offer_list', methods: ['GET'])]
    public function getAllOffres(): Response
    {
        $List_offer=$this->offer->findAll();

        return $this->json($List_offer,200);
    }
    #[Route('/new', name: 'app_offer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, OfferRepository $offerRepository): Response
    {
        $offer = new Offer();
       /* $offerproduct=new OfferProductType();
        $offerproduct->setMaxItems(25);
        $offerproduct->setPrice(255);

        $offer->addOfferProductType($offerproduct);*/
        $form = $this->createForm(OfferType::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $offerRepository->save($offer, true);

            return $this->redirectToRoute('app_offer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('offer/new.html.twig', [
            'offer' => $offer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_offer_show', methods: ['GET'])]
    public function show(Offer $offer): Response
    {
        return $this->render('offer/show.html.twig', [
            'offer' => $offer,
        ]);
    }
    //add a controller
    #[Route('/{id}/offers', name: 'app_Show_offer_For_seller', methods: ['GET'])]
    public function showOffer(Offer $offer): Response
    {
        return $this->render('offer/show.html.twig', [
            'offer' => $offer,
        ]);
    }




    #[Route('/{id}/edit', name: 'app_offer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Offer $offer, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(OfferType::class, $offer);

        // Store original offerProductTypes for removal check
        $originalOfferProductTypes = new ArrayCollection();
        foreach ($offer->getOfferProductTypes() as $offerProductTypes) {
            $originalOfferProductTypes->add($offerProductTypes);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Remove offerProductTypes that were removed from the form
            foreach ($originalOfferProductTypes as $offerProductTypes) {
                if (false === $offer->getOfferProductTypes()->contains($offerProductTypes)) {
                    // Check if offer product type has any associated child records
                    if ($offerProductTypes->getProductType()) {
                        // Remove the association with the child record first to avoid foreign key constraint errors
                        $offerProductTypes->getProductType()->removeOfferProductType($offerProductTypes);
                    }
                    $manager->remove($offerProductTypes);
                }
            }

            // Add new offerProductTypes
            foreach ($form->get('offerProductTypes')->getData() as $offerProductTypes) {
                $offerProductTypes->setOffer($offer);
                $manager->persist($offerProductTypes);
            }

            $manager->flush();

            return $this->redirectToRoute('app_offer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('offer/edit.html.twig', [
            'offer' => $offer,
            'form' => $form,
        ]);
    }



    #[Route('/{id}', name: 'app_offer_delete', methods: ['POST'])]
    public function delete(Request $request, Offer $offer, OfferRepository $offerRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$offer->getId(), $request->request->get('_token'))) {
            $offerRepository->remove($offer, true);
        }

        return $this->redirectToRoute('app_offer_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{id}/OfferProductsTypes', name: 'app_offerProductTypes', methods: ['GET'])]
    public function showOfferProductTypes(int $id): Response
    {

        $offer =  $this->doctrine
            ->getRepository(Offer::class)
            ->find($id);
       // $offerProductTypes = $offer->getOfferProductTypes();

        if (!$offer) {
            throw $this->createNotFoundException(
                'No offer found for id '.$id
            );
        }

        return $this->render('offer/show_Offer_Product.html.twig', [
            'offer' => $offer,
           // 'offerProductTypes' => $offerProductTypes

        ]);
    }
//add a controller

    #[Route('/{id}/OfferProductsTypesForSeller', name: 'app_offerProductTypes_ForSeller', methods: ['GET'])]
    public function showOfferProductTypesForSeller(int $id): Response
    {

        $offer =  $this->doctrine
            ->getRepository(Offer::class)
            ->find($id);
        // $offerProductTypes = $offer->getOfferProductTypes();

        if (!$offer) {
            throw $this->createNotFoundException(
                'No offer found for id '.$id
            );
        }

        return $this->render('seller/dashboard/offer_product.html.twig', [
            'offer' => $offer,
            // 'offerProductTypes' => $offerProductTypes

        ]);
    }

//
    public function __construct(private Security $security,private ManagerRegistry $doctrine,EntityManagerInterface $manager, OfferRepository $offer) {
        $this->em=$doctrine;
        $this->offer=$offer;
        $this->manager=$manager;
    }





}
