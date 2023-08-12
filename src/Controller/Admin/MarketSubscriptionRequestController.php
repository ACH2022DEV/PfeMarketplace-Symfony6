<?php

namespace App\Controller\Admin;

use App\Entity\Continent;
use App\Entity\MarketSubscriptionRequest;
use App\Entity\User;
use App\Form\MarketSubscriptionRequestType;
use App\Repository\ContinentRepository;
use App\Repository\MarketSubscriptionRequestRepository;
use App\Repository\UserRepository;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Mosquitto\Client;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;
use Symfony\Component\Process\Process;
use Symfony\Component\Mime\BodyRendererInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/market/subscription/request')]
class MarketSubscriptionRequestController extends AbstractController
{
    protected $flashy;
    protected $translator;
    private $exempleService;

    public function __construct(FlashyNotifier $flashy, TranslatorInterface $translator)
    {
        $this->flashy = $flashy;
        $this->translator = $translator;



    }
    #[Route('/', name: 'app_market_subscription_request_index', methods: ['GET'])]
    public function index(MarketSubscriptionRequestRepository $marketSubscriptionRequestRepository,
                          Request $request,
                          UserRepository $userRepository,

    ): Response

    {
//        if (!$authChecker->isGranted('ROLE_SUPER_ADMIN')) {
//            return $this->redirectToRoute('app_login_seller');
//        }

        return $this->render('market_subscription_request/index.html.twig', [
            'market_subscription_requests' => $marketSubscriptionRequestRepository->findAll(),
            'users' => $userRepository->findByExampleField("ROLE_SELLER")

        ]);

    }

    #[Route('/new', name: 'app_market_subscription_request_new', methods: ['GET', 'POST'])]
    public function new(Request $request, MarketSubscriptionRequestRepository $marketSubscriptionRequestRepository  ): Response
    {
        $marketSubscriptionRequest = new MarketSubscriptionRequest();
        $form = $this->createForm(MarketSubscriptionRequestType::class, $marketSubscriptionRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {



            $marketSubscriptionRequestRepository->save($marketSubscriptionRequest, true);
//            $user = new User();
//            $now = new \DateTime();
//            $user->setCreatedAt($now->format('Y-m-d H:i:s'));
            return $this->redirectToRoute('app_Home_seller', [], Response::HTTP_SEE_OTHER);
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'new.html.twig';

        return $this->renderForm('market_subscription_request/'.$template, [
            'market_subscription_request' => $marketSubscriptionRequest,
            'form' => $form,
        ]);
    }



    #[Route('/{id}', name: 'app_market_subscription_request_show', methods: ['GET'])]
    public function show(MarketSubscriptionRequest $marketSubscriptionRequest ): Response
    {
//        if (!$authChecker->isGranted('ROLE_SUPER_ADMIN')) {
//            return $this->redirectToRoute('app_login_seller');
//        }
        return $this->render('market_subscription_request/show.html.twig', [
            'market_subscription_request' => $marketSubscriptionRequest,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_market_subscription_request_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MarketSubscriptionRequest $marketSubscriptionRequest, MarketSubscriptionRequestRepository $marketSubscriptionRequestRepository ): Response
    {
//        if (!$authChecker->isGranted('ROLE_SUPER_ADMIN')) {
//            return $this->redirectToRoute('app_login_seller');
//        }
        $form = $this->createForm(MarketSubscriptionRequestType::class, $marketSubscriptionRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $marketSubscriptionRequestRepository->save($marketSubscriptionRequest, true);

            return $this->redirectToRoute('app_market_subscription_request_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('market_subscription_request/edit.html.twig', [
            'market_subscription_request' => $marketSubscriptionRequest,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_market_subscription_request_delete', methods: ['POST'])]
    public function delete(Request $request, MarketSubscriptionRequest $marketSubscriptionRequest, MarketSubscriptionRequestRepository $marketSubscriptionRequestRepository): Response
    {
//        if (!$authChecker->isGranted('ROLE_SUPER_ADMIN')) {
//            return $this->redirectToRoute('app_login_seller');
//        }
        if ($this->isCsrfTokenValid('delete'.$marketSubscriptionRequest->getId(), $request->request->get('_token'))) {
            $marketSubscriptionRequestRepository->remove($marketSubscriptionRequest, true);
        }


        return $this->redirectToRoute('app_market_subscription_request_index', [], Response::HTTP_SEE_OTHER);
    }





    #[Route('/rejected/{id}', name: 'app_market_subscription_request_rejected', methods: ['GET','POST'])]
    public function rejected (Request $request, MarketSubscriptionRequest $marketSubscriptionRequest,MarketSubscriptionRequestRepository $marketSubscriptionRequestRepository ): Response
    {
//        if (!$authChecker->isGranted('ROLE_SUPER_ADMIN')) {
//            return $this->redirectToRoute('app_login_seller');
//        }

        if ($marketSubscriptionRequest->getStatus() === "pending") {
            $marketSubscriptionRequest->setStatus("rejected");
            $marketSubscriptionRequestRepository->save($marketSubscriptionRequest, true);
            if ($request->isXmlHttpRequest()) {
                $html = $this->render('@MercurySeriesFlashy/flashy.html.twig');
                return new Response($html->getContent(), Response::HTTP_OK);
            }
        }

        return $this->redirectToRoute('app_market_subscription_request_index', [], Response::HTTP_SEE_OTHER);
    }

}
