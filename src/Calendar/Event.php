<?php

namespace Calendar;

use Calendar\Event\TimeSpan;
use Calendar\Expression\ExpressionInterface;
use Calendar\Expression\Parser;
use Calendar\View\CalendarView;
use DateTime;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Event
{
    /** @var UuidInterface */
    protected $id;

    /** @var string */
    protected $name;

    /** @var ExpressionInterface|null */
    protected $expression;

    /** @var TimeSpan|null */
    protected $timespan;

    protected function __construct(UuidInterface $id, string $name, ?ExpressionInterface $expression = null, ?TimeSpan $time = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->expression = $expression;
        $this->timespan = $time;
    }

    public static function create(UuidInterface $id, string $name, $expression, $time) : self
    {
        if(null !== $expression) {
            if(false === ($expression instanceof ExpressionInterface)) {
                $expression  = Parser::fromString((string) $expression);
            }
        }

        if(null !== $time) {
            if(false === ($time instanceof TimeSpan)) {
                $time = TimeSpan::fromString((string) $time);
            }
        }

        return new self($id, $name, $expression, $time);
    }

    public function isMatching(DateTime $date) : bool
    {
        return $this->expression->isMatching($date);
    }

    public function duration() : int
    {
        return $this->timespan->minutes();
    }

    public function name() : string
    {
        return $this->name;
    }

    public function toString() : string
    {
        return (string) $this->id->toString() . ":" . $this->expression;
    }

    public function __toString() : string
    {
        return $this->toString();
    }

    public function id() : UuidInterface
    {
        return $this->id;
    }

    public function updateExpression(ExpressionInterface $expression) : void
    {
        $this->expression = $expression;
    }

    public function timespan() : ?TimeSpan
    {
        return $this->timespan;
    }

    public function expression() : ?ExpressionInterface
    {
        return $this->expression;
    }
}