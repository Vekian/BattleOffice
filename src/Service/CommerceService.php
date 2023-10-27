<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CommerceService {
    public function __construct(private EntityManagerInterface $entityManager, private ParameterBagInterface $parameterBag) {

    }

    public function saveOrder($order){
        $yourApiKey = "mJxTXVXMfRzLg6ZdhUhM4F6Eutcm1ZiPk4fNmvBMxyNR4ciRsc8v0hOmlzA0vTaX";
        $httpClient = HttpClient::create();

        try {
            $response = $httpClient->request('POST', 'https://api-commerce.simplon-roanne.com/order', [
            'headers' => [
                'Authorization' => 'Bearer ' . $yourApiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                "order" => [
                    "id" => $order->getId(),
                    "product" => $order->getProduct()->getName(),
                    "payment_method" => $order->getPaymentMethod(),
                    "status" => strtoupper($order->getStatus()),
                    "client" => [
                        "firstname" => $order->getAddressBilling()->getFirstname(),
                        "lastname" => $order->getAddressBilling()->getLastname(),
                        "email" => $order->getClient()->getEmail(),
                    ],
                    "addresses" => [
                        "billing" => [
                            "address_line1" => $order->getAddressBilling()->getAddressLine1(),
                            "address_line2" => $order->getAddressBilling()->getAddressLine2(),
                            "city" => $order->getAddressBilling()->getCity(),
                            "zipcode" => $order->getAddressBilling()->getZipcode(),
                            "country" => $order->getAddressBilling()->getCountry(),
                            "phone" => $order->getAddressBilling()->getPhone(),
                        ],
                        "shipping" => [
                            "address_line1" => $order->getAddressShipping()->getAddressLine1(),
                            "address_line2" => $order->getAddressShipping()->getAddressLine2(),
                            "city" => $order->getAddressShipping()->getCity(),
                            "zipcode" => $order->getAddressShipping()->getZipcode(),
                            "country" => $order->getAddressShipping()->getCountry(),
                            "phone" => $order->getAddressShipping()->getPhone(),
                        ],
                    ]
                ],
            ]
        ]);
        } catch (\Exception $e) {
            return 'Exception: ' . $e->getMessage() . "\n";
        }

        $responseData = $response->toArray();
        return $responseData;
    }  

    public function updateOrder($orderId) {
        $yourApiKey = "mJxTXVXMfRzLg6ZdhUhM4F6Eutcm1ZiPk4fNmvBMxyNR4ciRsc8v0hOmlzA0vTaX";
        $httpClient = HttpClient::create();
        $url = "https://api-commerce.simplon-roanne.com/order/" . $orderId . "/status";
        try {
            $response = $httpClient->request('POST', $url , [
            'headers' => [
                'Authorization' => 'Bearer ' . $yourApiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                "status" => "PAID",
            ]
        ]);
        } catch (\Exception $e) {
            return 'Exception: ' . $e->getMessage() . "\n";
        }
        $responseData = $response->toArray();
        return $responseData;
    }
}

?>