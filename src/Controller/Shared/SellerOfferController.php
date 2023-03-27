<?php

namespace App\Controller\Shared;

use App\Entity\Offer;
use App\Entity\Seller;
use App\Entity\SellerOffer;
use App\Form\SellerOfferType;
use App\Repository\SellerOfferRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;


#[Route('/sellerOffer')]
class SellerOfferController extends AbstractController
{
    private $em;
    public function __construct(  private Security $security,private ManagerRegistry $doctrine,SellerOfferRepository $sellerOfferRepository){
        $this->em=$doctrine;
        $this->sellerOfferRepository = $sellerOfferRepository;

    }
    #[Route('/', name: 'app_seller_offer_index', methods: ['GET'])]
    public function index(SellerOfferRepository $sellerOfferRepository): Response
    {
        return $this->render('seller_offer/index.html.twig', [
            'seller_offers' => $sellerOfferRepository->findAll(),
        ]);
    }
    //previous code of app_seller_offer_new


    #[Route('/{id}', name: 'app_seller_offer_show', methods: ['GET'])]
    public function show(SellerOffer $sellerOffer): Response
    {
        return $this->render('seller_offer/show.html.twig', [
            'seller_offer' => $sellerOffer,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_seller_offer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SellerOffer $sellerOffer, SellerOfferRepository $sellerOfferRepository): Response
    {
        $form = $this->createForm(SellerOfferType::class, $sellerOffer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sellerOfferRepository->save($sellerOffer, true);

            return $this->redirectToRoute('app_seller_offer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('seller_offer/edit.html.twig', [
            'seller_offer' => $sellerOffer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_seller_offer_delete', methods: ['POST'])]
    public function delete(Request $request, SellerOffer $sellerOffer, SellerOfferRepository $sellerOfferRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sellerOffer->getId(), $request->request->get('_token'))) {
            $sellerOfferRepository->remove($sellerOffer, true);
        }

        return $this->redirectToRoute('app_seller_offer_index', [], Response::HTTP_SEE_OTHER);
    }
    //confirmer une commande
    #[Route('/new', name: 'app_seller_offer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SellerOfferRepository $sellerOfferRepository): Response
    {
        $sellerOffer = new SellerOffer();
        $form = $this->createForm(SellerOfferType::class, $sellerOffer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sellerOfferRepository->save($sellerOffer, true);

            return $this->redirectToRoute('app_seller_offer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('seller_offer/new.html.twig', [
            'seller_offer' => $sellerOffer,
            'form' => $form,
        ]);
    }
//add cart

//end panier

}
