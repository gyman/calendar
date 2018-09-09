<?php

namespace Calendar;

use Calendar\DomainEvents\CalendarCreated;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventSourcing\AggregateRoot;
use Ramsey\Uuid\UuidInterface;

final class Calendar extends AggregateRoot
{
    /** @var UuidInterface */
    protected $id;

    /** @var string */
    protected $name;

    /** @var Event[]|array|Collection */
    protected $events;

    /** @var DateTime */
    protected $updatedAt;

    /** @var DateTime */
    protected $createdAt;

    public static function create(UuidInterface $id, string $name) : Calendar
    {
        $calendar = new self();
        $calendar->recordThat(CalendarCreated::withData($id, $name));

        return $calendar;
    }

    public function whenCalendarCreated(CalendarCreated $event)
    {
        $this->id = $event->id();
        $this->name = $event->name();
        $this->events = new ArrayCollection();
        $this->createdAt = $this->updatedAt = new DateTime();
    }

    protected function aggregateId(): string
    {
        return $this->id()->toString();
    }

    protected function apply(AggregateChanged $event): void
    {
        $method = 'when' . get_class_last_part($event);
        $this->{$method}($event);

        $this->updatedAt = Carbon::now();
    }


    public function id(): UuidInterface
    {
        return $this->id;
    }

    public function filterEvents(DateTime $date) : Collection
    {
        return $this->events->filter(function(Event $event) use ($date) : bool {
            return $event->isMatching($date);
        });
    }

    public function getOccurrences(DateTime $start, DateTime $end) : array
    {
        $period = new DatePeriod($start, new DateInterval('P1D'), $end);

        $result = [];

        foreach($period as $day) {
            foreach($this->events as $event) {
                if($event->isMatching($day)) {
                    $result[] = new Occurrence($day, $event);
                }
            }
        }

        return $result;
    }

    public function addEvent(Event $event) : void
    {
        $this->events->add($event);
        $this->updatedAt = new DateTime();
    }

    public function removeEvent(Event $event) : void
    {
        foreach($this->events as $key => $value) {
            if($value === $event) {
                unset($this->events[$key]);
                break;
            }
        }

        $this->updatedAt = new DateTime();
    }

    public function name(): string
    {
        return $this->name;
    }

    public function count() : int
    {
        return count($this->events);
    }

    public function matchingEvents(DateTime $date) : Collection
    {
        return $this->events->filter(function(Event $event) use ($date) : bool {
            return $event->isMatching($date);
        });
    }

    public function events() : Collection
    {
        return $this->events;
    }

    public function getEventByName(string $name) : ?Event
    {
        foreach($this->events as $event) {
            if($event->name() === $name) {
                return $event;
            }
        }

        return null;
    }

    public function toArray() : array
    {
        return [
            "id" => $this->id()->toString()
        ];
    }
}