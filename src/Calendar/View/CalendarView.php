<?php

namespace Calendar\View;

use Calendar\Event;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\UuidInterface;

class CalendarView
{
    /** @var UuidInterface */
    protected $id;

    /** @var string */
    protected $name;

    /** @var Event[]|Collection */
    protected $events;

    public function __construct(UuidInterface $id, string $name, ?Collection $events = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->events = $events ?? new ArrayCollection;
    }

    public function id(): UuidInterface
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function events() : Collection
    {
        return $this->events;
    }
}