<?php

namespace App\Controller\Admin\Currency;

use App\Entity\Currency;
use App\Form\CurrencyType;
use App\Repository\CurrencyRepository;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/currency')]
class CurrencyController extends AbstractController
{
    #[Route('/', name: 'app_admin_currency_currency_index', methods: ['GET'])]
    public function index(CurrencyRepository $currencyRepository,Request $request): Response
    {

        $template = $request->query->get('ajax') ? '_list.html.twig' : 'index.html.twig';

        return $this->render('admin/currency/currency/' . $template, [
            'currencies' => $currencyRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_currency_currency_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CurrencyRepository $currencyRepository, FlashyNotifier $flashy,TranslatorInterface $translator): Response
    {
        $currency = new Currency();
        $form = $this->createForm(CurrencyType::class, $currency);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currencyRepository->add($currency, true);
            $flashy->message( $translator->trans('Message.Standard.SuccessSave'));
            if ($request->isXmlHttpRequest()) {
                $html = $this->render('@MercurySeriesFlashy/flashy.html.twig');
                return new Response($html->getContent(), Response::HTTP_OK);
            }

            return $this->redirectToRoute('app_admin_currency_currency_index', [], Response::HTTP_SEE_OTHER);
        }
        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'new.html.twig';
        return $this->renderForm('admin/currency/currency/' . $template, [
            'currency' => $currency,
            'form' => $form,
        ],
            new Response(
                null,
                $form->isSubmitted() && !$form->isValid() ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK,
            ));
    }

    #[Route('/{code}', name: 'app_admin_currency_currency_show', methods: ['GET'])]
    public function show(Currency $currency): Response
    {

        return $this->render('admin/currency/currency/show.html.twig', [
            'currency' => $currency,
        ]);
    }

    #[Route('/{code}/edit', name: 'app_admin_currency_currency_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Currency $currency, CurrencyRepository $currencyRepository, FlashyNotifier $flashy,TranslatorInterface $translator): Response
    {
        $form = $this->createForm(CurrencyType::class, $currency);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currencyRepository->add($currency, true);
            $flashy->message( $translator->trans('Message.Standard.SuccessSave'));
            if ($request->isXmlHttpRequest()) {
                $html = $this->render('@MercurySeriesFlashy/flashy.html.twig');
                return new Response($html->getContent(), Response::HTTP_OK);
            }

            return $this->redirectToRoute('app_admin_currency_currency_index', [], Response::HTTP_SEE_OTHER);
        }
        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'edit.html.twig';
        return $this->renderForm('admin/currency/currency/' . $template, [
            'currency' => $currency,
            'form' => $form,
        ],
            new Response(
                null,
                $form->isSubmitted() && !$form->isValid() ?  Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK,
            ));
    }

    #[Route('/{code}', name: 'app_admin_currency_currency_delete', methods: ['POST'])]
    public function delete(Request $request, Currency $currency, CurrencyRepository $currencyRepository, FlashyNotifier $flashy,TranslatorInterface $translator): Response
    {
        if ($this->isCsrfTokenValid('delete'.$currency->getCode(), $request->request->get('_token'))) {
            $currencyRepository->remove($currency, true);
        }

        return $this->redirectToRoute('app_admin_currency_currency_index', [], Response::HTTP_SEE_OTHER);
    }
}
