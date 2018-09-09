<?php

namespace Calendar\DomainEvents;

use Prooph\EventSourcing\AggregateChanged;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class CalendarCreated extends AggregateChanged
{
    public static function withData(UuidInterface $uuid, string $name): self
    {
        return self::occur($uuid->toString(), [
            'id' => $uuid->toString(),
            'name' => $name
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
}