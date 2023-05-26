<?php

namespace App\Controller;

use App\Entity\ApiProductClick;
use App\Form\ApiProductClickType;
use App\Repository\ApiProductClickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/product/click')]
class ApiProductClickController extends AbstractController
{
    #[Route('/', name: 'app_api_product_click_index', methods: ['GET'])]
    public function index(ApiProductClickRepository $apiProductClickRepository): Response
    {
        return $this->render('api_product_click/index.html.twig', [
            'api_product_clicks' => $apiProductClickRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_api_product_click_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ApiProductClickRepository $apiProductClickRepository): Response
    {
        $apiProductClick = new ApiProductClick();
        $form = $this->createForm(ApiProductClickType::class, $apiProductClick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $apiProductClickRepository->save($apiProductClick, true);

            return $this->redirectToRoute('app_api_product_click_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('api_product_click/new.html.twig', [
            'api_product_click' => $apiProductClick,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_api_product_click_show', methods: ['GET'])]
    public function show(ApiProductClick $apiProductClick): Response
    {
        return $this->render('api_product_click/show.html.twig', [
            'api_product_click' => $apiProductClick,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_api_product_click_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ApiProductClick $apiProductClick, ApiProductClickRepository $apiProductClickRepository): Response
    {
        $form = $this->createForm(ApiProductClickType::class, $apiProductClick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $apiProductClickRepository->save($apiProductClick, true);

            return $this->redirectToRoute('app_api_product_click_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('api_product_click/edit.html.twig', [
            'api_product_click' => $apiProductClick,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_api_product_click_delete', methods: ['POST'])]
    public function delete(Request $request, ApiProductClick $apiProductClick, ApiProductClickRepository $apiProductClickRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$apiProductClick->getId(), $request->request->get('_token'))) {
            $apiProductClickRepository->remove($apiProductClick, true);
        }

        return $this->redirectToRoute('app_api_product_click_index', [], Response::HTTP_SEE_OTHER);
    }
}
