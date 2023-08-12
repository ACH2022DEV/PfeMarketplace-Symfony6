<?php

namespace App\Controller;

use App\Entity\Api;
use App\Entity\Seller;
use App\Form\ApiType;
use App\Repository\ApiRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[Route('/api')]
class ApiController extends AbstractController
{
    #[Route('/', name: 'app_api_index', methods: ['GET'])]
    public function index(ApiRepository $apiRepository, Request $request): Response
    {
//        if (!$authChecker->isGranted('ROLE_SUPER_ADMIN')) {
//            return $this->redirectToRoute('app_login_seller');
//        }
        $template = $request->query->get('ajax') ? '_list.html.twig' : 'index.html.twig';
        return $this->render('api/'.$template, [
            'apis' => $apiRepository->findAll(),
        ]);
    }

    #[Route('/new/{seller?null}', name: 'app_api_new', methods: ['GET', 'POST'])]
    public function     new(Request $request,
                            ApiRepository $apiRepository,
                            Seller $seller

    ): Response
    {
//        if (!$authChecker->isGranted('ROLE_SUPER_ADMIN')) {
//            return $this->redirectToRoute('app_login_seller');
//        }
        $api = new Api();
        $form = $this->createForm(ApiType::class, $api);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $seller->setApi($api);
            $apiRepository->save($api, true);

            return $this->redirectToRoute('app_seller_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'new.html.twig';
        return $this->renderForm('api/'.$template, [
            'api' => $api,
            'form' => $form,
        ],
            new Response(
                null,
                $form->isSubmitted() && !$form->isValid() ? 422 : 200,
            ));
    }

    #[Route('/{id}', name: 'app_api_show', methods: ['GET'])]
    public function show(Api $api): Response
    {
//        if (!$authChecker->isGranted('ROLE_SUPER_ADMIN')) {
//            return $this->redirectToRoute('app_login_seller');
//        }
        return $this->render('api/show.html.twig', [
            'api' => $api,
        ]);
    }



    #[Route('/{id}/edit', name: 'app_api_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request,
                         Api $api,
                         ApiRepository $apiRepository,

    ): Response
    {
//        if (!$authChecker->isGranted('ROLE_SUPER_ADMIN')) {
//            return $this->redirectToRoute('app_login_seller');
//        }
        $form = $this->createForm(ApiType::class, $api);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $apiRepository->save($api, true);

            return $this->redirectToRoute('app_seller_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'edit.html.twig';

        return $this->renderForm('api/'.$template, [
            'api' => $api,
            'form' => $form,
        ],
            new Response(
                null,
                $form->isSubmitted() && !$form->isValid() ? 422 : 200,
            ));
    }

    #[Route('/{id}', name: 'app_api_delete', methods: ['POST'])]
    public function delete(Request $request, Api $api, ApiRepository $apiRepository): Response
    {
//        if (!$authChecker->isGranted('ROLE_SUPER_ADMIN')) {
//            return $this->redirectToRoute('app_login_seller');
//        }
        if ($this->isCsrfTokenValid('delete'.$api->getId(), $request->request->get('_token'))) {
            $apiRepository->remove($api, true);
        }

        return $this->redirectToRoute('app_api_index', [], Response::HTTP_SEE_OTHER);
    }
}
