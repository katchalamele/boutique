<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use App\Event\PaymentSuccessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PaymentSuccessSubscriber implements EventSubscriberInterface
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            'payment.success' => 'sendSuccessEmail'
        ];
    }

    public function sendSuccessEmail(PaymentSuccessEvent $paymentSuccessEvent)
    {
        $this->logger->info('Email envoyé pour la commande N°' . $paymentSuccessEvent->getPurchase()->getId());
    }
}
