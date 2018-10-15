<?php

namespace Calendar\Handler;

use App\Event\CalendarCreated;
use App\Repository\CalendarRepository;
use Calendar\Calendar;
use Calendar\Command\CreateCalendar;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CreateCalendarHandler
{
    /** @var CalendarRepository */
    protected $calendarRepository;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(CalendarRepository $calendarRepository, EventDispatcherInterface $eventDispatcher)
    {
        $this->calendarRepository = $calendarRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(CreateCalendar $command)
    {
        $calendar = Calendar::create($command->id(), $command->name());

        $this->calendarRepository->save($calendar);

        $this->eventDispatcher->dispatch(CalendarCreated::NAME, new CalendarCreated($command->id()));
    }
}