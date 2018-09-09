<?php

namespace Calendar\Handler;

use Calendar\Calendar;
use Calendar\Command\AddEvent;
use Calendar\Command\UpdateEvent;
use Calendar\Event;
use Calendar\Expression\Builder;
use Calendar\Repository\CalendarViewRepositoryInterface;
use Calendar\Repository\EventRepositoryInterface;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

class UpdateEventHandler
{
    /** @var CalendarViewRepositoryInterface */
    protected $calendarRepository;

    /** @var EventRepositoryInterface */
    protected $eventRepository;

    public function __construct(CalendarViewRepositoryInterface $calendarRepository, EventRepositoryInterface $eventRepository)
    {
        $this->calendarRepository = $calendarRepository;
        $this->eventRepository = $eventRepository;
    }

    public function handle(UpdateEvent $command)
    {
        /** @var Calendar $calendar */
        $calendar = $this->calendarRepository->findById($command->calendarId());
        Assert::notNull($calendar, 'Calendar does not exists');

        $event = $this->eventRepository->findById($command->eventId());
        Assert::notNull($event, 'Event does not exists');

        $calendar->addEvent($event);

        $this->calendarRepository->save($calendar);
        $this->eventRepository->save($event);
    }
}