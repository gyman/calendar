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
        $collection = new ArrayCollection();

        $calendar = new Calendar(Uuid::uuid4(), 'test', $collection);

        $event1 = new Event(Uuid::uuid4(),$calendar, 'test', DayOfWeek::monday(),  TimeSpan::fromString("12:00-13:00"));
        $event2 = new Event(Uuid::uuid4(),$calendar, 'test', DayOfWeek::tuesday(),  TimeSpan::fromString("12:00-13:00"));
        $event3 = new Event(Uuid::uuid4(),$calendar, 'test', Parser::fromString("monday or tuesday or wednesday or thursday or friday or saturday or sunday"),  TimeSpan::fromString("12:00-13:00"));

        $collection->add($event1);
        $collection->add($event2);
        $collection->add($event3);

        $result = $calendar->filterEvents(new DateTime("last monday"));

        $this->assertCount(2, $result);
        $this->assertEquals($event1, $result[0]);
        $this->assertEquals($event3, $result[2]);
    }

    public function testAddEvent()
    {
        $collection = new ArrayCollection([]);
        $calendar = new Calendar(Uuid::uuid4(), 'test', $collection);
        $calendar->addEvent(new Event(Uuid::uuid4(), $calendar, 'test', Parser::fromString("monday or tuesday or wednesday or thursday or friday or saturday or sunday"),  TimeSpan::fromString("12:00-13:00")));

        $this->assertCount(1, $collection);
    }

    public function testGetOccurrences()
    {
        $collection = new ArrayCollection();
        $calendar = new Calendar(Uuid::uuid4(), 'test', $collection);

        $event = new Event(Uuid::uuid4(), $calendar, 'test', DayOfWeek::monday(), TimeSpan::fromString("12:00-13:00"));

        $collection->add($event);

        $result = $calendar->getOccurrences(new DateTime("01.06.2018"), new DateTime("30.06.2018"));

        $this->assertCount(4, $result);
        $this->assertEquals(new DateTime("04.06.2018"), $result[0]->date());
        $this->assertEquals(new DateTime("11.06.2018"), $result[1]->date());
        $this->assertEquals(new DateTime("18.06.2018"), $result[2]->date());
        $this->assertEquals(new DateTime("25.06.2018"), $result[3]->date());
    }

    public function testGetOccurrencesEmptyResult()
    {
        $collection = new ArrayCollection();

        $calendar = new Calendar(Uuid::uuid4(),'test',  $collection);

        $event = new Event(Uuid::uuid4(), $calendar, 'test', DayOfWeek::monday(), TimeSpan::fromString("12:00-13:00"));

        $collection->add($event);

        $result = $calendar->getOccurrences(new DateTime("last tuesday"), new DateTime("last tuesday"));

        $this->assertCount(0, $result);
    }

    public function testGetOccurrencesOnSmallerPeriod()
    {
        $collection = new ArrayCollection();

        $calendar = new Calendar(Uuid::uuid4(), 'test', $collection);

        $event = new Event(Uuid::uuid4(), $calendar, 'test', new AndOperator(
            DayOfWeek::monday(),
            new AndOperator(
                new After(new DateTime("01.01.2018")),
                new Before(new DateTime("31.12.2018"))
            )
        ), TimeSpan::fromString("12:00-13:00"));

        $collection->add($event);

        $result = $calendar->getOccurrences(new DateTime("01.06.2018"), new DateTime("30.06.2018"));

        $this->assertCount(4, $result);
        $this->assertEquals(new DateTime("04.06.2018"), $result[0]->date());
        $this->assertEquals(new DateTime("11.06.2018"), $result[1]->date());
        $this->assertEquals(new DateTime("18.06.2018"), $result[2]->date());
        $this->assertEquals(new DateTime("25.06.2018"), $result[3]->date());
    }

    public function testGetOccurrencesOnBiggerPeriod()
    {
        $collection = new ArrayCollection();

        $calendar = new Calendar(Uuid::uuid4(),'test',  $collection);

        $event = new Event(Uuid::uuid4(), $calendar,'test', new AndOperator(
            DayOfWeek::monday(),
            new AndOperator(
                new After(new DateTime("01.06.2018")),
                new Before(new DateTime("30.06.2018"))
            )
        ), TimeSpan::fromString("12:00-13:00"));

        $collection->add($event);

        $result = $calendar->getOccurrences(new DateTime("01.01.2018"), new DateTime("31.12.2018"));

        $this->assertCount(4, $result);
        $this->assertEquals(new DateTime("04.06.2018"), $result[0]->date());
        $this->assertEquals(new DateTime("11.06.2018"), $result[1]->date());
        $this->assertEquals(new DateTime("18.06.2018"), $result[2]->date());
        $this->assertEquals(new DateTime("25.06.2018"), $result[3]->date());
    }
}
