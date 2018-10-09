<?php

namespace Calendar\Handler;

use Calendar\Calendar;
use Calendar\Command\CreateEvent;
use Calendar\Event;
use Calendar\Repository\CalendarRepositoryInterface;
use Webmozart\Assert\Assert;

class CreateEventHandler
{
    /** @var CalendarRepositoryInterface */
    protected $calendarRepository;

    public function __construct(CalendarRepositoryInterface $calendarRepository)
    {
        $this->calendarRepository = $calendarRepository;
    }

    public function handle(CreateEvent $command)
    {
        /** @var Calendar $calendar */
        $calendar = $this->calendarRepository->get($command->calendarId());

        Assert::notNull($calendar, 'Calendar does not exists');

        $event = Event::create($command->eventId(), $command->name(), $command->expressions(), $command->timeSpan());

        $calendar->addEvent($event);

        $this->calendarRepository->save($calendar);
    }
}