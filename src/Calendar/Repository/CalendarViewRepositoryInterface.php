<?php

namespace Calendar\Repository;

use Calendar\View\CalendarView;
use Ramsey\Uuid\UuidInterface;

interface CalendarViewRepositoryInterface
{
    public function findByName(string $name): ?CalendarView;

    public function findById(UuidInterface $fromString) : ?CalendarView;
}