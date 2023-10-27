<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Order;
use App\Entity\Client;
use App\Form\OrderType;
use Psr\Log\LoggerInterface;
use App\Repository\ProductRepository;
use App\Service\CommerceService;
use App\Service\StripeService;
use App\Service\MailService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;


class LandingPageController extends AbstractController
{
    private $commerceService;
    private $stripeService;
    private $mailService;
    public function __construct(CommerceService $commerceService, StripeService $stripeService, MailService $mailService)
    {
        $this->commerceService = $commerceService;
        $this->stripeService = $stripeService;
        $this->mailService = $mailService;
    }

    #[Route('/', name: 'landing_page')]
    public function index(Request $request, EntityManagerInterface $entityManager, ProductRepository $productRepository): Response
    {
        // Ton code ici
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $client = $order->getClient();
            $entityManager->persist($client);
            $entityManager->flush();

            $addressBilling = $order->getAddressBilling();
            $entityManager->persist($addressBilling);
            $entityManager->flush();
            
            if ($form->getData()->getAddressShipping()->getFirstname() === null){
                $order->setAddressShipping($addressBilling);
            }
            else {
                $addressShipping = $order->getAddressShipping();
                $entityManager->persist($addressShipping);
                $entityManager->flush();
            }
            $product = $productRepository->find($request->request->get('product'));
            $order->setProduct($product);
            $order->setPaymentMethod($request->request->get('payment_method'));
            $order->setStatus('waiting');
            $entityManager->persist($order);
            $entityManager->flush();

            $id = $order->getId();

            return $this->redirectToRoute('checkout', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        return $this->render('landing_page/index_new.html.twig', [
            'order' => $order,
            'form' => $form,
            'products' => $productRepository->findAll()
        ]);
    }

    #[Route('/confirmation', name: 'confirmation')]
    public function confirmation(): Response
    {
        return $this->render('landing_page/confirmation.html.twig');
    }

    #[Route('/order/{id}', name: 'checkout', methods: ['GET', 'POST'])]
    public function show(Order $order): Response
    {
        $response = $this->commerceService->saveOrder($order);
        $session = $this->stripeService->startPayment($response['order_id'], $order);
        return $this->redirect($session->url);
    }
}