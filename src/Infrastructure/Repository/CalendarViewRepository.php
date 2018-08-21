<?php

namespace App\Repository;

use Calendar\Calendar;
use Calendar\Repository\CalendarViewRepositoryInterface;
use Doctrine\ORM\EntityRepository;
use Ramsey\Uuid\UuidInterface;

class CalendarViewRepository extends EntityRepository implements CalendarViewRepositoryInterface
{
    public function save(Calendar $calendar): void
    {
        $this->getEntityManager()->persist($calendar);
        $this->getEntityManager()->flush();
    }

    public function findById(UuidInterface $uuid): ?Calendar
    {
        return $this->find($uuid);
    }

    public function findByName(string $name): ?Calendar
    {
        return $this->findOneByName($name);
    }
}