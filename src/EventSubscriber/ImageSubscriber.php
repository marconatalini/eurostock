<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 02/02/2020
 * Time: 10:06
 */

namespace App\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Event\Events as VichEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class ImageSubscriber implements EventSubscriberInterface
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents(): array
    {
        // return the subscribed events, their methods and priorities
        return [
            VichEvents::PRE_REMOVE => 'onVichImagePreRemove',
        ];
    }

    public function onVichImagePreRemove(Event $event)
    {

    }

}