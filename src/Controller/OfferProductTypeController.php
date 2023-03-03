<?php

namespace App\Controller;

use App\Entity\OfferProductType;
//use App\Entity\ProductType;
use App\Form\OfferProdType;
use App\Repository\OfferProductTypeRepository;
//use App\Repository\ProductTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/offer/product')]
class OfferProductTypeController extends AbstractController
{
    #[Route('/', name: 'app_offer_product_index', methods: ['GET'])]
    public function index(OfferProductTypeRepository $offerProductTypeRepository): Response
    {
        return $this->render('offer_product/index.html.twig', [
            'offer_product_types' => $offerProductTypeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_offer_product_new', methods: ['GET', 'POST'])]
    //,ProductTypeRepository $ProductTypeRepository
    public function new(Request $request, OfferProductTypeRepository $offerProductTypeRepository): Response
    {
        $offerProductType = new OfferProductType();
      //  $productType = new ProductType();
        $form = $this->createForm(OfferProdType::class, $offerProductType );
        //$form = $this->createForm(ProductType::class, $productType  );
        $form->handleRequest($request);
//&& $form2->isSubmitted() && $form2->isValid()
        if ($form->isSubmitted() && $form->isValid() )  {
            $offerProductTypeRepository->save($offerProductType, true);
          //  $ProductTypeRepository->save($productType, true);
            return $this->redirectToRoute('app_offer_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('offer_product/new.html.twig', [
            'offer_product_type' => $offerProductType,
            //'product_type' => $productType,
            'form' => $form,
            //'form2' => $form2,
        ]);
    }

    #[Route('/{id}', name: 'app_offer_product_show', methods: ['GET'])]
    public function show(OfferProductType $offerProductType): Response
    {
        return $this->render('offer_product/show.html.twig', [
            'offer_product_type' => $offerProductType,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_offer_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, OfferProductType $offerProductType, OfferProductTypeRepository $offerProductTypeRepository): Response
    {
        $form = $this->createForm(OfferProdType::class, $offerProductType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $offerProductTypeRepository->save($offerProductType, true);

            return $this->redirectToRoute('app_offer_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('offer_product/edit.html.twig', [
            'offer_product_type' => $offerProductType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_offer_product_delete', methods: ['POST'])]
    public function delete(Request $request, OfferProductType $offerProductType, OfferProductTypeRepository $offerProductTypeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$offerProductType->getId(), $request->request->get('_token'))) {
            $offerProductTypeRepository->remove($offerProductType, true);
        }

        return $this->redirectToRoute('app_offer_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
