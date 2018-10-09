<?php

namespace Calendar\Repository;

use Calendar\Calendar;
use Ramsey\Uuid\UuidInterface;

interface CalendarRepositoryInterface
{
    public function save(Calendar $calendar) : void;

    public function get(UuidInterface $id) : ?Calendar;
}