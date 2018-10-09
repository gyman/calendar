<?php

namespace Test;

use Calendar\Event;
use Calendar\Event\TimeSpan;
use Calendar\Expression\Builder;
use Calendar\View\CalendarView;
use Calendar\View\EventView;
use DateTime;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class TestEventView
{
    private const DEFAULT_NAME = 'event_test';
    private const DEFAULT_STARTDATE = "now";
    private const DEFAULT_ENDDATE = "+1 month";
    private const DEFAULT_FROM = "12:00";
    private const DEFAULT_TO = "13:00";

    /** @var UuidInterface */
    protected $id;

    /** @var string */
    protected $name;

    /** @var CalendarView */
    protected $calendar;

    /** @var array|string[] */
    protected $days;

    /** @var string */
    protected $after;

    /** @var string */
    protected $before;

    /** @var TimeSpan */
    protected $time;


    protected function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->name = self::DEFAULT_NAME;
        $this->calendar = new CalendarView(Uuid::uuid4(), "");
        $this->expression = null;
        $this->after = self::DEFAULT_STARTDATE;
        $this->before = self::DEFAULT_ENDDATE;
        $this->time = TimeSpan::fromString(self::DEFAULT_FROM . "-" . self::DEFAULT_TO);
    }

    public static function create() : self
    {
        return new self();
    }

    public function workingDays() : self
    {
        $this->days = ["monday", "tuesday", "wednesday", "thursday", "friday"];

        return $this;
    }

    public function weekend() : self
    {
        $this->days = ["saturday", "sunday"];

        return $this;
    }

    public function withCalendar(CalendarView $calendar) : self
    {
        $this->calendar = $calendar;

        return $this;
    }

    public function withName(string $name) : self
    {
        $this->name = $name;

        return $this;
    }

    public function withTime(string $from, string $to) : self
    {
        $this->time = Timespan::fromString($from . "-" . $to);

        return $this;
    }

    public function withStart(string $string) : self
    {
        $this->after = $string;

        return $this;
    }

    public function withEnd(string $string) : self
    {
        $this->before = $string;

        return $this;
    }

    public function event() : EventView
    {
        $expression = Builder::create()
            ->setStartDate(new DateTime($this->after))
            ->setEndDate(new DateTime($this->before))
            ->setDays($this->days)
            ->expression();

        return new EventView($this->id, $this->calendar, $this->name, $expression, $this->time);
    }

    public function everyDay() : self
    {
        $this->days = ["monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday"];

        return $this;
    }
}