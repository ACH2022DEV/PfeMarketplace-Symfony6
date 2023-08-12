<?php

namespace App\Controller\Admin;


use App\Entity\ProductType;
use App\Form\ProductTypeType;
use App\Repository\ApiProductClickRepository;
use App\Repository\ApiProductRepository;
use App\Repository\ProductTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


#[Route('/product/type')]
class ProductTypeController extends AbstractController
{
    private $em;
    #[Route('/', name: 'app_product_type_index', methods: ['GET'])]
    public function index(ProductTypeRepository $productTypeRepository): Response
    {
//, AuthorizationCheckerInterface $authChecker
//        if (!$authChecker->isGranted('ROLE_SUPER_ADMIN')) {
//            return $this->redirectToRoute('app_login_seller');
//        }
        return $this->render('product_type/index.html.twig', [
            'product_types' => $productTypeRepository->findAll(),
        ]);
    }
    //add a controller
  /*  #[Route('/AllOffer', name: 'app_product_seller', methods: ['GET'] )]
    public function offerseller(ProductTypeRepository $productTypeRepository): Response
    {

        return $this->render('seller/dashboard/home_seller.html.twig', [
            'product_types' => $productTypeRepository->findAll(),
        ]);
    }*/

    //

    #[Route('/new', name: 'app_product_type_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProductTypeRepository $productTypeRepository): Response
    {
        $productType = new ProductType();
        $form = $this->createForm(ProductTypeType::class, $productType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productTypeRepository->save($productType, true);

            return $this->redirectToRoute('app_product_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product_type/new.html.twig', [
            'product_type' => $productType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_type_show', methods: ['GET'])]
    public function show(ProductType $productType): Response
    {
        return $this->render('product_type/show.html.twig', [
            'product_type' => $productType,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ProductType $productType, ProductTypeRepository $productTypeRepository): Response
    {
        $form = $this->createForm(ProductTypeType::class, $productType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productTypeRepository->save($productType, true);

            return $this->redirectToRoute('app_product_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product_type/edit.html.twig', [
            'product_type' => $productType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_type_delete', methods: ['POST'])]
    public function delete(Request $request, ProductType $productType, ProductTypeRepository $productTypeRepository, ApiProductRepository $apiProductRepository, ApiProductClickRepository $apiProductClickRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$productType->getId(), $request->request->get('_token'))) {
            // Rechercher les produits de type de produit à supprimer
            $apiProducts = $apiProductRepository->findBy(['productType' => $productType]);

            // Parcourir chaque produit et supprimer les ApiProductClick correspondants
            foreach ($apiProducts as $apiProduct) {
                $apiProductClicks = $apiProductClickRepository->findBy(['apiProduct' => $apiProduct]);
                foreach ($apiProductClicks as $apiProductClick) {
                    $apiProductClickRepository->remove($apiProductClick, true);
                }
            }

            // Supprimer les produits de type de produit
            foreach ($apiProducts as $apiProduct) {
                $apiProductRepository->remove($apiProduct, true);
            }

            // Supprimer le type de produit
            $productTypeRepository->remove($productType, true);
        }

        return $this->redirectToRoute('app_product_type_index', [], Response::HTTP_SEE_OTHER);
    }
    //add a controller
    #[Route('/{id}/OfferProductsSeller', name: 'offerProductTypes_seller', methods: ['GET'])]
    public function showOfferProductSeller(int $id): Response
    {

        $productType =  $this->doctrine
            ->getRepository(ProductType::class)
            ->find($id);
        //$offerProductTypes = $productType->getProductTypeidProductType();

        if (!$productType) {
            throw $this->createNotFoundException(
                'No productType found for id '.$id
            );
        }

        return $this->render('seller/dashboard/offer_product.html.twig', [
            'productType' => $productType,
            //  'offerProductTypes' => $offerProductTypes

        ]);
    }

    //end
    #[Route('/{id}/OfferProductsTypes', name: 'offerProductTypes_ProductType', methods: ['GET'])]
    public function showOfferProductTypes(int $id): Response
    {

        $productType =  $this->doctrine
            ->getRepository(ProductType::class)
            ->find($id);
         //$offerProductTypes = $productType->getProductTypeidProductType();

        if (!$productType) {
            throw $this->createNotFoundException(
                'No productType found for id '.$id
            );
        }

        return $this->render('product_type/show_OffreProductType.html.twig', [
            'productType' => $productType,
          //  'offerProductTypes' => $offerProductTypes

        ]);
    }
    public function __construct(private readonly ManagerRegistry $doctrine) {}
}
