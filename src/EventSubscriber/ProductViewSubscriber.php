<?php

namespace App\EventSubscriber;

use App\Event\ProductViewEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductViewSubscriber implements EventSubscriberInterface
{

    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function sendProductViewEmail(ProductViewEvent $productViewEvent)
    {
        $this->logger->info('Le produit ' . $productViewEvent->getProduct()->getName() . ' a été consulté.');
    }

    public static function getSubscribedEvents()
    {
        return [
            'product.view' => 'sendProductViewEmail'
        ];
    }
}
