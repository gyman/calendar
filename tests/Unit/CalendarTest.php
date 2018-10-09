<?php

namespace Test\Unit;

use Calendar\Calendar;
use Calendar\Event;
use Calendar\Event\TimeSpan;
use Calendar\Expression\AndOperator;
use Calendar\Expression\DayOfWeek;
use Calendar\Expression\After;
use Calendar\Expression\Before;
use Calendar\Expression\Parser;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class CalendarTest extends TestCase
{

    public function testFilterEvents()
    {
        $calendar = Calendar::create(Uuid::uuid4(), 'test');

        $eventId1 = Uuid::uuid4();
        $eventId2 = Uuid::uuid4();
        $eventId3 = Uuid::uuid4();

        $calendar->addEvent(Event::create($eventId1,'test', "monday",  "12:00-13:00"));
        $calendar->addEvent(Event::create($eventId2,'test', "tuesday",  "12:00-13:00"));
        $calendar->addEvent(Event::create($eventId3,'test', "monday or tuesday or wednesday or thursday or friday or saturday or sunday",  "12:00-13:00"));

        $result = $calendar->filterEvents(new DateTime("last monday"));

        $this->assertCount(2, $result);
        $this->assertEquals($eventId1, $result[0]->id());
        $this->assertEquals($eventId3, $result[2]->id());
    }

    public function testAddEvent()
    {
        $calendar = Calendar::create(Uuid::uuid4(), 'test');
        $calendar->addEvent(Event::create(Uuid::uuid4(), 'test', "monday or tuesday or wednesday or thursday or friday or saturday or sunday", "12:00-13:00"));

        $this->assertCount(1, $calendar->events());
    }

    public function testGetOccurrences()
    {
        $calendar = Calendar::create(Uuid::uuid4(), 'test');

        $calendar->addEvent(Event::create(Uuid::uuid4(), 'test', "monday", "12:00-13:00"));

        $result = $calendar->getOccurrences(new DateTime("01.06.2018"), new DateTime("30.06.2018"));

        $this->assertCount(4, $result);
        $this->assertEquals(new DateTime("04.06.2018"), $result[0]->date());
        $this->assertEquals(new DateTime("11.06.2018"), $result[1]->date());
        $this->assertEquals(new DateTime("18.06.2018"), $result[2]->date());
        $this->assertEquals(new DateTime("25.06.2018"), $result[3]->date());
    }

    public function testGetOccurrencesEmptyResult()
    {
        $calendar = Calendar::create(Uuid::uuid4(),'test');

        $calendar->addEvent(Event::create(Uuid::uuid4(), 'test', "monday", "12:00-13:00"));

        $result = $calendar->getOccurrences(new DateTime("last tuesday"), new DateTime("last tuesday"));

        $this->assertCount(0, $result);
    }

    public function testGetOccurrencesOnSmallerPeriod()
    {
        $calendar = Calendar::create(Uuid::uuid4(), 'test');

        $event = Event::create(Uuid::uuid4(), 'test', new AndOperator(
            DayOfWeek::monday(),
            new AndOperator(
                new After(new DateTime("01.01.2018")),
                new Before(new DateTime("31.12.2018"))
            )
        ), "12:00-13:00");

        $calendar->addEvent($event);

        $result = $calendar->getOccurrences(new DateTime("01.06.2018"), new DateTime("30.06.2018"));

        $this->assertCount(4, $result);
        $this->assertEquals(new DateTime("04.06.2018"), $result[0]->date());
        $this->assertEquals(new DateTime("11.06.2018"), $result[1]->date());
        $this->assertEquals(new DateTime("18.06.2018"), $result[2]->date());
        $this->assertEquals(new DateTime("25.06.2018"), $result[3]->date());
    }

    public function testGetOccurrencesOnBiggerPeriod()
    {
        $calendar = Calendar::create(Uuid::uuid4(),'test');

        $event = Event::create(Uuid::uuid4(),'test', new AndOperator(
            DayOfWeek::monday(),
            new AndOperator(
                new After(new DateTime("01.06.2018")),
                new Before(new DateTime("30.06.2018"))
            )
        ), "12:00-13:00");

        $calendar->addEvent($event);

        $result = $calendar->getOccurrences(new DateTime("01.01.2018"), new DateTime("31.12.2018"));

        $this->assertCount(4, $result);
        $this->assertEquals(new DateTime("04.06.2018"), $result[0]->date());
        $this->assertEquals(new DateTime("11.06.2018"), $result[1]->date());
        $this->assertEquals(new DateTime("18.06.2018"), $result[2]->date());
        $this->assertEquals(new DateTime("25.06.2018"), $result[3]->date());
    }
}
