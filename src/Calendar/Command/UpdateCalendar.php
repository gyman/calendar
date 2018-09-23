<?php

namespace Calendar\Command;

use Calendar\Event\TimeSpan;
use DateTime;
use Ramsey\Uuid\UuidInterface;

class UpdateCalendar
{
    /** @var UuidInterface */
    protected $eventId;

    /** @var UuidInterface */
    protected $calendarId;

    /** @var string */
    protected $name;

    /** @var DateTime|null */
    protected $endDate;

    /** @var TimeSpan */
    protected $timeSpan;

    protected function __construct(?UuidInterface $eventId = null, ?UuidInterface $calendarId = null, ?string $name = null, ?DateTime $endDate = null, ?TimeSpan $timeSpan = null)
    {
        $this->eventId = $eventId;
        $this->calendarId = $calendarId;
        $this->name = $name;
        $this->endDate = $endDate;
        $this->timeSpan = $timeSpan;
    }

    public static function withData(UuidInterface $eventId, UuidInterface $calendarId, string $name, ?DateTime $endDate, TimeSpan $timeSpan) : self
    {
        return new self(...func_get_args());
    }

    public static function empty() : self
    {
        return new self();
    }

    public function calendarId(): ?UuidInterface
    {
        return $this->calendarId;
    }

    public function name(): ?string
    {
        return $this->name;
    }

    public function endDate(): ?DateTime
    {
        return $this->endDate;
    }

    public function timeSpan(): ?TimeSpan
    {
        return $this->timeSpan;
    }

    public function eventId(): ?UuidInterface
    {
        return $this->eventId;
    }
}