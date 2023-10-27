<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;


class PaypalService {

    public function __construct(private EntityManagerInterface $entityManager, private ParameterBagInterface $parameterBag) {

    }

    public function getPaypalClient(){
        $clientId="AVN5mVrD22pw2HMC2nqzMJ22iUOim4cR-tE6D_bqFH2lLa4jK6_XBZMe9OxJkeXvLK85tLcCJPV_Mkqm";
        $clientSecret="EE5lRIXAtKM6WDwGsa0Z3_pzpC8txIwtyuRSigqX_ogmMlSofHZ6YTp1Say-8X4VWgBNyakuH6Pqsmf0";
        $environnement = new SandboxEnvironment($clientId, $clientSecret);
        return new PayPalHttpClient($environnement);
    }

    public function startPayment($orderId, $order){
        $product = $order->getProduct();
        if ($product->getPriceFree() !== null){
            $price = strval(intval($product->getPriceFree()));
          }
          else {
            $price = strval(intval($product->getPrice()));
          }
        $items[] = [
            'name' => $product->getName(),
            'quantity' => "1",
            'unit_amount' => [
                'value' => $price,
                'currency_code' => "EUR"
            ]
            ];
        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');
        $request->body = [
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "reference_id" => "test_ref_id1",
                    "amount" => [
                        "value" => $price,
                        "currency_code" => "EUR",
                        "breakdown" => [
                            'item_total' => [
                                'currency_code' => "EUR",
                                'value' => $price
                            ]
                        ]
                    ],
                    'items' => $items,
                    'custom_id' => $orderId
                ]
            ],
            "application_context" => [
                "cancel_url" => "http://battleoffice.dvl.to",
                "return_url" => "http://battleoffice.dvl.to/confirmation?orderId=" . $orderId ."&orderDataId=" . $order->getId()
            ] 
        ];
        $client = $this->getPaypalClient();
        $response = $client->execute($request);
        
        $linkOk = "";
        foreach ($response->result->links as $link) {
            if ($link->rel === "approve") {
                $linkOk = $link->href;
                break;
            }
        }
        return $linkOk;
    }
}