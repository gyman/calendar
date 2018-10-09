<?php

namespace Calendar\DomainEvents;

use Calendar\Event;
use Calendar\Event\TimeSpan;
use Calendar\Expression\ExpressionInterface;
use Calendar\Expression\Parser;
use Prooph\EventSourcing\AggregateChanged;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class EventCreated extends AggregateChanged
{
    public static function withEvent(UuidInterface $calendarId, Event $event): self
    {
        return self::occur($calendarId, [
            "id" => $event->id(),
            "name" => $event->name(),
            "expression" => (string) $event->expression(),
            "timespan" => (string) $event->timespan()
        ]);
    }

    public function id(): UuidInterface
    {
        return Uuid::fromString($this->payload['id']);
    }

    public function name(): string
    {
        return $this->payload['name'];
    }

    public function expression(): ExpressionInterface
    {
        return Parser::fromString($this->payload['expression']);
    }

    public function timespan(): TimeSpan
    {
        return TimeSpan::fromString($this->payload['timespan']);
    }
}