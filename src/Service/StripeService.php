<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Stripe\Stripe;
use Stripe\Checkout\Session;


class StripeService {

    public function __construct(private EntityManagerInterface $entityManager, private ParameterBagInterface $parameterBag) {

    }

    public function startPayment($orderId, $order){
        Stripe::setApiKey('sk_test_51NuFINFuMIbivSImr6RUMV5yDQ5K0U4QJDezXxYHMCCqNCR1HVUEMRpquIUWpUEODdlNXkvMgMVgeOr4v1MDNtzg00vghaisyq');

        $product = $order->getProduct();
        if ($product->getPriceFree() !== null){
          $price = strval(intval($product->getPriceFree() * 100));
        }
        else {
          $price = strval(intval($product->getPrice() * 100));
        }
        $name = $product->getName();
        $orderData_id = $order->getId();
        $session = Session::create([
            'mode' => "payment",
            'success_url' => 'http://battleoffice.dvl.to/confirmation',
            'cancel_url' => 'http://battleoffice.dvl.to/',
            'line_items' => [
                [
                  'quantity' => 1,
                  'price_data' => [
                    'currency' => 'EUR',
                    'product_data' => [
                        'name' => $name
                    ],
                    'unit_amount' => $price,
                  ]
                ],
              ],
            'metadata' => [
                'order_id' => $orderId,
                'orderData_id' => $orderData_id,
            ],
        ]);
         return $session;
    }
}

?>