<?php

namespace Calendar\Repository;

use Calendar\Calendar;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\UuidInterface;

interface CalendarViewRepositoryInterface
{
    public function findById(UuidInterface $uuid): ?Calendar;

    public function findByName(string $name): ?Calendar;
}