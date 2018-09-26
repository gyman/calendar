<?php

namespace App\Repository;

use Calendar\Calendar;
use Calendar\Repository\CalendarViewRepositoryInterface;
use Calendar\View\CalendarView;
use Doctrine\ORM\EntityRepository;
use Ramsey\Uuid\UuidInterface;

class CalendarViewRepository extends EntityRepository implements CalendarViewRepositoryInterface
{
    public function findById(UuidInterface $uuid): ?CalendarView
    {
        return $this->find($uuid);
    }

    public function findByName(string $name): ?CalendarView
    {
        return $this->findOneByName($name);
    }
}