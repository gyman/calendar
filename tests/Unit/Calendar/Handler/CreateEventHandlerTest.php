<?php
namespace Test\Unit\Calendar\Handler;

use Calendar\Calendar;
use Calendar\Command\CreateEvent;
use Calendar\Event\TimeSpan;
use Calendar\Expression\Parser;
use Calendar\Handler\CreateEventHandler;
use Calendar\Repository\CalendarRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class CreateEventHandlerTest extends TestCase
{

    public function testHandle()
    {
        $calendarId = Uuid::uuid4();
        $eventId = Uuid::uuid4();
        $calendar = Calendar::create($calendarId, "");

        $calendarRepository = $this->prophesize(CalendarRepositoryInterface::class);
        $calendarRepository->save($calendar);
        $calendarRepository->get($calendarId)->willReturn($calendar);

        $handler = new CreateEventHandler($calendarRepository->reveal());

        $handler->handle(CreateEvent::withData(
            $calendarId,
            $eventId,
            "some name",
            Parser::fromString("after 2018-01-01 and before 2018-01-31 and (monday or wednesday or friday)"),
            TimeSpan::fromString("11:00-12:00")
        ));

        $this->assertCount(1, $calendar->events());
    }
}
