<?php

namespace Calendar\Command;

use Calendar\Event\TimeSpan;
use DateTime;
use Ramsey\Uuid\UuidInterface;

class AddEvent
{
    /** @var UuidInterface */
    protected $calendarId;

    /** @var string */
    protected $name;

    /** @var DateTime */
    protected $startDate;

    /** @var DateTime|null */
    protected $endDate;

    /** @var TimeSpan */
    protected $timeSpan;

    /** @var array */
    protected $days;

    protected function __construct(?UuidInterface $calendarId = null, ?string $name = "", ?DateTime $startDate = null, ?DateTime $endDate = null, ?TimeSpan $timeSpan = null, ?array $days = [])
    {
        $this->calendarId = $calendarId;
        $this->name = $name;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->timeSpan = $timeSpan;
        $this->days = $days;
    }

    public static function withData(UuidInterface $calendarId, string $name, DateTime $startDate, ?DateTime $endDate, TimeSpan $timeSpan, array $days) : self
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

    public function name(): string
    {
        return $this->name;
    }

    public function startDate(): ?DateTime
    {
        return $this->startDate;
    }

    public function endDate(): ?DateTime
    {
        return $this->endDate;
    }

    public function timeSpan(): ?TimeSpan
    {
        return $this->timeSpan;
    }

    public function days(): array
    {
        return $this->days;
    }
}