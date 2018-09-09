<?php

namespace Calendar\Command;

use Ramsey\Uuid\UuidInterface;

class CreateCalendar
{
    /** @var UuidInterface */
    protected $id;

    /** @var string */
    protected $name;

    public function __construct(UuidInterface $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function id(): UuidInterface
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }
}