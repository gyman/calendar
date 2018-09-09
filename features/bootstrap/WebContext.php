<?php

use Calendar\Calendar;
use Calendar\Repository\CalendarRepositoryInterface;
use Calendar\Repository\CalendarViewRepositoryInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\BrowserKit\Client;
use App\Kernel;
use Symfony\Component\DomCrawler\Crawler;
use Webmozart\Assert\Assert;

class WebContext extends AbstractContext
{
    /** @var Client */
    protected $client;

    /** @var string */
    private $lastException;

    public function __construct(Kernel $kernel)
    {
        parent::__construct($kernel);

        $this->client = $this->get('test.client');
        $this->client->disableReboot();
    }

    protected function createCalendar(string $id, string $name)
    {
        $this->call("POST", "/calendar", [
            "id" => $id,
            "name" => $name
        ]);
    }

    protected function getCalendar(UuidInterface $id): Calendar
    {
        $json = $this->call("GET", "/calendar/" . $id);

        $data = json_decode($json, JSON_OBJECT_AS_ARRAY);
        Assert::eq(json_last_error(), JSON_ERROR_NONE, 'Error decoding json: ' . $json);

        $this->get(CalendarRepositoryInterface::class)->get(Uuid::fromString($data["id"]));
    }

    protected function getCalendars(): array
    {
        return $this->get(CalendarViewRepositoryInterface::class)->findAll();
    }

    protected function addEvent(UuidInterface $id, string $name, string $expression, string $hours)
    {
        $this->call("POST", "/calendar/" . $id . "/event", [
            "json_data" => json_encode([
                "name" => $name,
                "expression" => $expression,
                "hours" => $hours
            ])
        ]);
    }

    protected function removeEvent(string $id, string $eventName)
    {
        $this->call("DELETE", "/calendar/" . $id . "/event/" . $eventName);
    }

    protected function call(string $method, string $url, array $options = []) : Crawler
    {
        $crawler = $this->client->request($method, $url, $options, [], [
            "CONTENT_TYPE" => 'application/json'
        ]);

        if(500 === $this->client->getResponse()->getStatusCode()) {
            $response = json_decode($this->client->getResponse()->getContent(), JSON_OBJECT_AS_ARRAY);
            $this->lastException = $response["errors"]["exception"];
        }

        return $crawler;
    }

    protected function getEvents(UuidInterface $id): array
    {
        /** @var Calendar $calendar */
        $calendar = $this->get(CalendarRepositoryInterface::class)->get($id);

        return $calendar->events()->toArray();
    }
}