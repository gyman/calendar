<?php

namespace App\Repository;

use Calendar\Calendar;
use Prooph\EventSourcing\Aggregate\AggregateRepository;
use Ramsey\Uuid\UuidInterface;

class CalendarRepository extends AggregateRepository
{
    public function save(Calendar $calendar): void
    {
        $this->saveAggregateRoot($calendar);
    }

    public function get(UuidInterface $id): ?Calendar
    {
        return $this->getAggregateRoot($id->toString());
    }
}