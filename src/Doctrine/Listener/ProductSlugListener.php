<?php

namespace App\Doctrine\Listener;

use App\Entity\Product;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductSlugListener
{
    protected $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function prePersist(Product $product, LifecycleEventArgs $event)
    {
        /*
        $entity = $event->getObject();

        if (!$entity instanceof Product) {
            return;
        }
        */

        if (empty($product->getSlug())) {
            $product->setSlug(strtolower($this->slugger->slug($product->getName())));
        }
    }
}
