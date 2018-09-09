<?php

namespace Calendar\Handler;

use Calendar\Calendar;
use Calendar\Command\AddEvent;
use Calendar\Event;
use Calendar\Expression\Builder;
use Calendar\Repository\CalendarViewRepositoryInterface;
use Calendar\Repository\EventRepositoryInterface;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

class CreateEventHandler
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

    public function handle(AddEvent $command)
    {
        /** @var Calendar $calendar */
        $calendar = $this->calendarRepository->findById($command->calendarId());

        Assert::notNull($calendar, 'Calendar does not exists');

        $expression = Builder::create()
            ->setStartDate($command->startDate())
            ->setEndDate($command->endDate())
            ->setDays($command->days())
            ->expression()
        ;

        $event = Event::create(Uuid::uuid4(), $calendar, $command->name(), $expression, $command->timeSpan());

        $calendar->addEvent($event);

        $this->calendarRepository->save($calendar);
        $this->eventRepository->save($event);
    }
}