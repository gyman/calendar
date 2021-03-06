<?php

namespace Test\Unit\Calendar\Expression;

use Calendar\Expression\OrOperator;
use Calendar\Expression\DayOfWeek;
use Calendar\Expression\After;
use Calendar\Expression\Before;
use DateTime;
use PHPUnit\Framework\TestCase;

class OrOperatorTest extends TestCase
{

    public function testToString()
    {
        $expression = new OrOperator(DayOfWeek::monday(), DayOfWeek::wednesday());

        $this->assertEquals("(monday or wednesday)", (string) $expression);
    }

    public function testIsMatchingOne()
    {
        $expression = new OrOperator(DayOfWeek::tuesday(), DayOfWeek::friday());

        $this->assertTrue($expression->isMatching(new DateTime("tuesday")));
    }
    
    public function testIsMatchingAll()
    {
        $expression = new OrOperator(DayOfWeek::tuesday(), DayOfWeek::tuesday());

        $this->assertTrue($expression->isMatching(new DateTime("tuesday")));
    }

    public function testIsNotMatchingOnlyFew()
    {
        $expression = new OrOperator(
            new DayOfWeek((int) (new DateTime("yesterday"))->format("w")),
            new DayOfWeek((int) (new DateTime("today"))->format("w"))
        );

        $this->assertTrue($expression->isMatching(new DateTime("today")));

        $expression = new OrOperator(
            new After(new DateTime("tomorrow")),
            new Before(new DateTime("yesterday"))
        );

        $this->assertFalse($expression->isMatching(new DateTime("today")));
    }
}
