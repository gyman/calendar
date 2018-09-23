<?php

namespace Calendar\Command;

use Calendar\Event\TimeSpan;
use Calendar\Expression\ExpressionInterface;
use Ramsey\Uuid\UuidInterface;

class CreateEvent
{
    /** @var UuidInterface */
    protected $calendarId;

    /** @var UuidInterface */
    protected $eventId;

    /** @var string */
    protected $name;

    /** @var ExpressionInterface */
    protected $expressions;

    /** @var TimeSpan */
    protected $timespan;

    protected function __construct(UuidInterface $calendarId, UuidInterface $eventId, string $name, ExpressionInterface $expression, ?TimeSpan $timeSpan)
    {
        $this->eventId = $eventId;
        $this->calendarId = $calendarId;
        $this->name = $name;
        $this->expressions = $expression;
        $this->timeSpan = $timeSpan;
    }

    public static function withData(UuidInterface $calendarId, UuidInterface $eventId, string $name, ExpressionInterface $expression, ?TimeSpan $timeSpan) : self
    {
        return new self(...func_get_args());
    }

    public function calendarId(): ?UuidInterface
    {
        return $this->calendarId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function timeSpan(): ?TimeSpan
    {
        return $this->timeSpan;
    }

    public function expressions(): ExpressionInterface
    {
        return $this->expressions;
    }

    public function eventId(): UuidInterface
    {
        return $this->eventId;
    }
}