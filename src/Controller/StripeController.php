<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\StripeService;
use App\Service\CommerceService;
use App\Service\MailService;
use Psr\Log\LoggerInterface;
use App\Repository\ProductRepository;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class StripeController extends AbstractController
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

    #[Route('/stripe/{id}', name: 'app_stripe')]
    public function index(): Response
    {

        return $this->render('stripe/index.html.twig', [
            'controller_name' => 'StripeController',
        ]);
    }

    #[Route('/webhook', name: 'webhook')]
    public function webhook(LoggerInterface $logger, Request $request, OrderRepository $orderRepository, EntityManagerInterface $entityManager): Response
    {
        $endpoint_secret = "whsec_588283d88fcc1a369706913f8b1d3e5d78a9760b8df03bda6eaae9f5229cae17";
        $payload = $request->getContent();

        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];

        $event = null;

        $event = \Stripe\Webhook::constructEvent(
            $payload, $sig_header, $endpoint_secret
        );
        

        if ($event->type === "checkout.session.completed"){
            $order = $orderRepository->find($event->data->object->metadata->orderData_id);
            $order->setStatus('PAID');
            $entityManager->flush();
            $orderId = $event->data->object->metadata->order_id;
            $product = $order->getProduct();
            $email = $event->data->object->customer_details->email;
            $name = $event->data->object->customer_details->name;
            $this->commerceService->updateOrder($orderId);
            $this->mailService->sendEmail($email, $name, $product);
        }
        return $this->render('stripe/index.html.twig', [
            'controller_name' => 'StripeController',
        ]);
    }
}
