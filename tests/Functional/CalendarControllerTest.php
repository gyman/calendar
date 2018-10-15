<?php

namespace Test\Functional;

use Calendar\View\CalendarView;
use Ramsey\Uuid\Uuid;
use Test\TestEventView;

class CalendarControllerTest extends WebTestCase
{
    public function testGetEvents()
    {
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();

        $calendar = new CalendarView(Uuid::uuid4(), "moj testowy");
        $event = TestEventView::create()
            ->everyDay()
            ->withStart("2017-01-01")
            ->withEnd("2017-01-07")
            ->withCalendar($calendar)
            ->event();

        $em->persist($calendar);
        $em->persist($event);
        $em->flush();

        $client = static::createClient();

        $crawler = $client->request('GET', '/events/search?start=2017-01-01&end=2017-01-07');
        $response = $client->getResponse();

        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), JSON_OBJECT_AS_ARRAY);
        $this->assertCount(7, $data);
    }
}