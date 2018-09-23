<?php

namespace Calendar;

use Calendar\Event\TimeSpan;
use Calendar\Expression\ExpressionInterface;
use Calendar\Expression\Parser;
use DateTime;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Event
{
    /** @var UuidInterface */
    protected $id;

    /** @var string */
    protected $name;

    /** @var ExpressionInterface */
    protected $expression;

    /** @var TimeSpan */
    protected $timespan;

    /** @var DateTime */
    protected $updatedAt;

    /** @var DateTime */
    protected $createdAt;

    public function __construct(UuidInterface $id, string $name, ExpressionInterface $expression, TimeSpan $time)
    {
        $this->id = $id;
        $this->name = $name;
        $this->expression = $expression;
        $this->timespan = $time;
        $this->createdAt = $this->updatedAt = new DateTime();
    }

    public static function create(UuidInterface $id, string $name, string $expression, string $time)
    {
        return new self($id, $name, Parser::fromString($expression), TimeSpan::fromString($time));
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

    public function updateExpression(ExpressionInterface $expression)
    {
        $this->expression = $expression;
    }

    public function timespan() : TimeSpan
    {
        return $this->timespan;
    }
}