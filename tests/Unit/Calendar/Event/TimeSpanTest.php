<?php

namespace Test\Unit\Calendar\Event;

use Calendar\Event\Time;
use Calendar\Event\TimeSpan;
use PHPUnit\Framework\TestCase;

class TimeSpanTest extends TestCase
{
    public function testDuration()
    {
        $timeSpan = TimeSpan::fromString("11:00-12:00");

        $this->assertEquals(60, $timeSpan->minutes());

        $timeSpan = TimeSpan::fromString("11:00-12:35");

        $this->assertEquals(95, $timeSpan->minutes());

        $timeSpan = TimeSpan::fromString("11:00-12:35");

        $this->assertEquals(95, $timeSpan->minutes());
    }

    public function testStartAfterEnd()
    {
        $this->expectExceptionMessage("End hour can't be greater than starting hour!");

        $timeSpan = TimeSpan::fromString("12:00-11:00");
    }

    public function testExceedingHour()
    {
        $this->expectExceptionMessage("Expected a value between 0 and 23. Got:");

        $timeSpan = TimeSpan::fromString("24:00-12:00");
    }

    public function testExceedingMinutes()
    {
        $this->expectExceptionMessage("This is not a correct timespan expression, should be in 00:00-00:00 format.");

        $timeSpan = TimeSpan::fromString("00:150-12:00");
    }

    public function testMinimumDuration()
    {
        $this->expectExceptionMessage("Minimum duration is one minute!");

        $timeSpan = TimeSpan::fromString("00:00-00:00");
    }

    public function testToString()
    {
        $timeSpan = TimeSpan::fromString("11:00-12:00");

        $this->assertEquals("11:00-12:00", (string) $timeSpan);
    }
}
