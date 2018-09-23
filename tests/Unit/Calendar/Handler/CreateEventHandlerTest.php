<?php
namespace Test\Unit\Calendar\Handler;

use Calendar\Calendar;
use Calendar\Command\CreateEvent;
use Calendar\Event\TimeSpan;
use Calendar\Handler\CreateEventHandler;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Test\Infrastructure\InMemoryCalendarRepository;
use Test\Infrastructure\InMemoryEventRepository;

class CreateEventHandlerTest extends TestCase
{

    public function testHandle()
    {
        $calendars = new ArrayCollection();
        $events = new ArrayCollection();

        $calendarId = Uuid::uuid4();
        $calendar = new Calendar($calendarId, "");

        $calendarRepository = new InMemoryCalendarRepository($calendars);
        $calendarRepository->save($calendar);

        $handler = new CreateEventHandler($calendarRepository, new InMemoryEventRepository($events));

        $handler->handle(CreateEvent::withData(
            $calendarId,
            "some name",
            new DateTime("2018-01-01"),
            new DateTime("2018-01-31"),
            TimeSpan::fromString("11:00-12:00"),
            ["monday","wednesday","friday"]
        ));

        $this->assertCount(1, $events);
    }
}
