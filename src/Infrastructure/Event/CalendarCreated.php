<?php

namespace App\Event;

use Ramsey\Uuid\UuidInterface;
use Symfony\Component\EventDispatcher\Event;

class CalendarCreated extends Event
{
    const NAME = 'calendar.created';

    /** @var UuidInterface */
    protected $id;

    public function __construct(UuidInterface $id)
    {
        $this->id = $id;
    }

    public function id(): UuidInterface
    {
        return $this->id;
    }
}