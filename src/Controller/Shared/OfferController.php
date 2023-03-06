<?php

namespace App\Controller\Shared;

use App\Entity\Offer;
use App\Form\OfferType;
use App\Repository\OfferRepository;
use Proxies\__CG__\App\Entity\OfferProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;


#[Route('/offer')]
class OfferController extends AbstractController
{
    private $em;
    #[Route('/', name: 'app_offer_index', methods: ['GET'])]
    public function index(OfferRepository $offerRepository): Response
    {
        return $this->render('offer/index.html.twig', [
            'offers' => $offerRepository->findAll(),
        ]);
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

    #[Route('/{id}/edit', name: 'app_offer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Offer $offer, OfferRepository $offerRepository): Response
    {
        $form = $this->createForm(OfferType::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $offerRepository->save($offer, true);

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

    public function __construct(private ManagerRegistry $doctrine) {}

}
