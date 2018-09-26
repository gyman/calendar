<?php

namespace Calendar\View;

use Calendar\Event;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\UuidInterface;

class CalendarView
{
    /** @var UuidInterface */
    protected $id;

    /** @var string */
    protected $name;

    /** @var Event[]|Collection */
    protected $events = [];

    /** @var DateTime */
    protected $updatedAt;

    /** @var DateTime */
    protected $createdAt;

    public function id(): UuidInterface
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function events()
    {
        return $this->events;
    }

    public function updatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function createdAt(): DateTime
    {
        return $this->createdAt;
    }
}