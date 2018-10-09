<?php

namespace Calendar\View;

use Calendar\Event\TimeSpan;
use Calendar\Expression\ExpressionInterface;
use Ramsey\Uuid\UuidInterface;

class EventView
{
    /** @var UuidInterface */
    protected $id;

    /** @var CalendarView */
    protected $calendar;

    /** @var string */
    protected $name;

    /** @var ExpressionInterface */
    protected $expression;

    /** @var TimeSpan */
    protected $timespan;

    public function __construct(UuidInterface $id, CalendarView $calendar, string $name, ExpressionInterface $expression, TimeSpan $time)
    {
        $this->id = $id;
        $this->calendar = $calendar;
        $this->name = $name;
        $this->expression = $expression;
        $this->timespan = $time;
    }

    public function id(): UuidInterface
    {
        return $this->id;
    }

    public function calendar(): CalendarView
    {
        return $this->calendar;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function expression(): ExpressionInterface
    {
        return $this->expression;
    }

    public function timespan(): TimeSpan
    {
        return $this->timespan;
    }
}