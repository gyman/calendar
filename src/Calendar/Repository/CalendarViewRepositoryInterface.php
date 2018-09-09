<?php

namespace Calendar\Repository;

use Calendar\Calendar;
use Ramsey\Uuid\UuidInterface;

interface CalendarViewRepositoryInterface
{
    public function findByName(string $name): ?Calendar;

    public function findById(UuidInterface $fromString) : ?Calendar;
}