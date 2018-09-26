<?php

namespace Calendar\Event;

use DateTime;
use Exception;
use Webmozart\Assert\Assert;

class TimeSpan
{
    /** @var Time */
    protected $from;

    /** @var Time */
    protected $to;

    /**
     * @throws Exception
     */
    protected function __construct(Time $from, Time $to)
    {
        if($from->gt($to)) {
            throw new Exception("End hour can't be greater than starting hour!");
        }

        if($from->equals($to)) {
            throw new Exception("Minimum duration is one minute!");
        }

        $this->from = $from;
        $this->to = $to;
    }

    public static function fromString(string $string) : self
    {
        Assert::regex($string, "@\d{2}:\d{2}-\d{2}:\d{2}@", 'This is not a correct timespan expression, should be in 00:00-00:00 format.');

        list($start, $end) = explode("-", $string);

        Assert::notNull($start);
        Assert::notNull($end);

        return new self(Time::fromString($start), Time::fromString($end));
    }

    public static function fromDateTimes(DateTime $startHour, DateTime $endHour) : self
    {
        return new self(Time::fromString($startHour->format("H:i")), Time::fromString($endHour->format("H:i")));
    }

    public function minutes() : int
    {
        $diff = $this->to->toDateTime()->diff(
            $this->from->toDateTime()
        );

        $result = 0;

        if($diff->h > 0) {
            $result = $diff->h * 60;
        }

        return $result += $diff->i;
    }

    public function __toString() : string
    {
        return sprintf("%s-%s", $this->from, $this->to);
    }

    public function start() : Time
    {
        return $this->from;
    }

    public function end() : Time
    {
        return $this->to;
    }
}