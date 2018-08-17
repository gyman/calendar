<?php

namespace App\Repository;

use Calendar\Event;
use Calendar\Repository\EventRepositoryInterface;
use Doctrine\ORM\EntityRepository;
use Ramsey\Uuid\UuidInterface;

class EventRepository extends EntityRepository implements EventRepositoryInterface
{
    public function save(Event $event): void
    {
        $this->getEntityManager()->persist($event);
        $this->getEntityManager()->flush($event);
    }

    public function findById(UuidInterface $eventId): ?Event
    {
        return $this->find($eventId);
    }
}