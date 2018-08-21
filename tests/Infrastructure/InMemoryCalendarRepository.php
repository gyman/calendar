<?php

namespace Test\Infrastructure;

use Calendar\Calendar;
use Calendar\Repository\CalendarViewRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class InMemoryCalendarRepository implements CalendarViewRepositoryInterface
{
    /** @var Collection|null|Calendar[] */
    protected $calendars;

    public function __construct(?Collection $calendars = null)
    {
        $this->calendars = $calendars ?? new ArrayCollection();
    }

    public function save(Calendar $calendar): void
    {
        $this->calendars[$calendar->id()->toString()] = $calendar;
    }

    public function findById(UuidInterface $uuid) : ?Calendar
    {
        return $this->calendars[$uuid->toString()];
    }

    public function findAll() : Collection
    {
        return $this->calendars;
    }

    public function findByName(string $name) : ?Calendar
    {
        foreach($this->calendars as $calendar) {
            if($calendar->name() === $name) {
                return $calendar;
            }
        }

        return null;
    }

    public function find(UuidInterface $id) : ?Calendar
    {
        return $this->findById($id);
    }
}