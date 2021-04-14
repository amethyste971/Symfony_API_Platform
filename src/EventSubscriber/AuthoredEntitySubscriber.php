<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Entity\AuthoredEntityInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AuthoredEntitySubscriber implements EventSubscriberInterface
{
    /**
     *
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage){
        $this->tokenStorage = $tokenStorage;
    }

    public static function getSubscribedEvents(){
        return [
            KernelEvents::VIEW => ['getAuthenticatedUser', EventPriorities::PRE_WRITE] 
        ];
    }

    public function getAuthenticatedUser(ViewEvent $event) {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        /** @var UserInterface $author */
        $author = $this->tokenStorage->getToken()->getUser();


        // var_dump($entity);
        // var_dump($method);
        // var_dump(Request::METHOD_POST);
        // die;

        if (!$entity instanceof AuthoredEntityInterface || Request::METHOD_POST !== $method) {
            return;
        }

        $entity->setAuthor($author);

    }

}

