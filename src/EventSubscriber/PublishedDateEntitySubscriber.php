<?php

namespace App\EventSubscriber;

use App\Entity\PublishedDateEntityInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PublishedDateEntitySubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(){
        return [
            KernelEvents::VIEW => ['setDatePublished', EventPriorities::PRE_WRITE] 
        ];
    }

    public function setDatePublished(ViewEvent $event) {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        // var_dump($entity);
        // var_dump($method);
        // var_dump(Request::METHOD_POST);
        // die;

        if (!$entity instanceof PublishedDateEntityInterface || Request::METHOD_POST !== $method) {
            return;
        }

        $entity->setPublished(new \DateTime());

    }

}

