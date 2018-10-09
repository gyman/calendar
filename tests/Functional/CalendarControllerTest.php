<?php

namespace Test\Functional;

use Calendar\Calendar;
use Calendar\View\CalendarView;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Test\TestEventView;

class CalendarControllerTest extends WebTestCase
{
    protected function setUp()
    {
        static::bootKernel();

        /** @var EntityManagerInterface $em */
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $metaData = $em->getMetadataFactory()->getAllMetadata();

        $tool = new SchemaTool($em);
        $tool->dropSchema($metaData);
        $tool->createSchema($metaData);
    }


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

        $crawler = $client->request('GET', '/get-events?start=20170101&end=20170107');
        $response = $client->getResponse();

        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), JSON_OBJECT_AS_ARRAY);
        $this->assertCount(7, $data);
    }
}