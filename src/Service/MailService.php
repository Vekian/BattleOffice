<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class MailService {
    private MailerInterface $mailer;
    public function __construct(MailerInterface $mailer) {
        $this->mailer = $mailer;

    }

    public function sendEmail($mail, $name, $product){
        if ($product->getPriceFree() == null){
            $price = strval($product->getPriceFree());
          }
          else {
            $price = strval($product->getPrice());
          }
        $email = (new TemplatedEmail())
            ->from('votre@email.com')
            ->to($mail)
            ->subject('Sujet de l\'e-mail')
            ->htmlTemplate('emails/confirmation.html.twig') // Spécifiez le template Twig
            ->context([
                'name' => $name,
                'nameProduct' => $product->getName(),
                'price' => $price,
            ]);
            
        $this->mailer->send($email);
    }
}

?>