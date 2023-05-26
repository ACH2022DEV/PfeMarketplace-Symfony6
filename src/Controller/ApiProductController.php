<?php

namespace App\Controller;

use App\Entity\Api;
use App\Entity\ApiProduct;
use App\Entity\ProductType;
use App\Form\ApiProductType;
use App\Repository\ApiProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/product')]
class ApiProductController extends AbstractController
{
    private $manager;
    private $apiProductRepository;
    private $em;

    public function __construct(private Security $security, private ManagerRegistry $doctrine, EntityManagerInterface $manager, ApiProductRepository $apiProductRepository) {
        $this->em=$doctrine;
        $this->apiProductRepository=$apiProductRepository;
        $this->manager=$manager;
    }

    #[Route('/', name: 'app_api_product_index', methods: ['GET'])]
    public function index(ApiProductRepository $apiProductRepository): Response
    {
        return $this->render('api_product/index.html.twig', [
            'api_products' => $apiProductRepository->findAll(),
        ]);
    }
    #[Route('/GetAllapi_product', name: 'api_product_list', methods: ['GET'])]
    public function getApi_product(SerializerInterface $serializer): Response
    {
        $List_apiProductRepository=$this->apiProductRepository->findAll();
        $serializedapiProductRepository = $serializer->serialize($List_apiProductRepository, 'json', ['groups' => 'apiProduct']);
        $data = json_decode($serializedapiProductRepository, true);

        return $this->json($data,200);
    }
    #[Route('/new', name: 'app_api_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ApiProductRepository $apiProductRepository): Response
    {
        $apiProduct = new ApiProduct();
        $form = $this->createForm(ApiProductType::class, $apiProduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $apiProductRepository->save($apiProduct, true);

            return $this->redirectToRoute('app_api_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('api_product/new.html.twig', [
            'api_product' => $apiProduct,
            'form' => $form,
        ]);
    }
    #[Route('/newApiProduct', name: 'api_product_create', methods: ['POST'])]
    public function create(Request $request, ApiProductRepository $apiProductRepository): Response
    {
        $jsonData = json_decode($request->getContent(), true);
        $api=$jsonData['api'];
        $apiProduct = new ApiProduct();
       // $apiProduct->setApi($api);
        $apiProduct->setName($jsonData['name']);
        $apiProduct->setIdProductFromApi($jsonData['idProductFromApi']);
       // $apiProduct->setProductType($jsonData['productType']);
        $apiProductId = $jsonData['api'];
        $api = $this->em->getRepository(Api::class)->find($apiProductId);
        if (!$api) {
            return new JsonResponse(['error' => 'L\'API spécifiée est introuvable'], Response::HTTP_NOT_FOUND);
        }

        $apiProduct->setApi($api);

// Récupérer l'objet ProductType à partir de son identifiant
        $productTypeId = $jsonData['productType'];
        $productType = $this->em->getRepository(ProductType::class)->find($productTypeId);
        if (!$productType) {
            return new JsonResponse(['error' => 'Le type de produit spécifié est introuvable'], Response::HTTP_NOT_FOUND);
        }

        $apiProduct->setProductType($productType);
// Enregistrer l'objet ApiProduct dans la base de données

        $this->manager->persist($apiProduct);

        $this->manager->flush();

        return new JsonResponse(['message' => 'Produit créé avec succès'], JsonResponse::HTTP_CREATED);

    }

    #[Route('/{id}', name: 'app_api_product_show', methods: ['GET'])]
    public function show(ApiProduct $apiProduct): Response
    {
        return $this->render('api_product/show.html.twig', [
            'api_product' => $apiProduct,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_api_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ApiProduct $apiProduct, ApiProductRepository $apiProductRepository): Response
    {
        $form = $this->createForm(ApiProductType::class, $apiProduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $apiProductRepository->save($apiProduct, true);

            return $this->redirectToRoute('app_api_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('api_product/edit.html.twig', [
            'api_product' => $apiProduct,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_api_product_delete', methods: ['POST'])]
    public function delete(Request $request, ApiProduct $apiProduct, ApiProductRepository $apiProductRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$apiProduct->getId(), $request->request->get('_token'))) {
            $apiProductRepository->remove($apiProduct, true);
        }

        return $this->redirectToRoute('app_api_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
