<?php

use Ramsey\Uuid\UuidInterface;
use Symfony\Component\BrowserKit\Client;
use App\Kernel;
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
        $this->jsonCall("POST", "/calendar/" . $id, [
            "name" => $name
        ]);
    }

    protected function getCalendarData(UuidInterface $id): array
    {
        $this->jsonCall("GET", "/calendar/" . $id);
        $json = $this->client->getResponse()->getContent();

        $data = json_decode($json, JSON_OBJECT_AS_ARRAY);
        Assert::eq(json_last_error(), JSON_ERROR_NONE, 'Error decoding json: ' . $json);

        return $data;
    }

    protected function getCalendars(): array
    {
        return $this->jsonCall("GET", "/calendar");
    }

    protected function addEvent(string $calendarId, string $eventId, string $name, string $expression, string $hours)
    {
        $this->jsonCall("POST", "/calendar/" . $calendarId . "/event", [
                "id" => $eventId,
                "name" => $name,
                "expression" => $expression,
                "hours" => $hours
        ]);
    }

    protected function removeEvent(string $id, string $eventName)
    {
        $this->jsonCall("DELETE", "/calendar/" . $id . "/event/" . $eventName);
    }

    protected function jsonCall(string $method, string $url, array $data = [], array $options = []) : array
    {
        $this->client->request($method, $url, $options, [], [
            "CONTENT_TYPE" => 'application/json'
        ], json_encode($data));

        if(500 === $this->client->getResponse()->getStatusCode()) {
            $response = json_decode($this->client->getResponse()->getContent(), JSON_OBJECT_AS_ARRAY);
            $this->lastException = $response["errors"]["exception"];
        }

        $json = $this->client->getResponse()->getContent();

        $data = json_decode($json, JSON_OBJECT_AS_ARRAY);

        if(json_last_error() !== JSON_ERROR_NONE) {
            trigger_error('Error decoding json: ' . $json);
        };

        return $data;
    }

    protected function getEvents(UuidInterface $calendarId): array
    {
        return $this->jsonCall("GET", "/calendar/" . $calendarId . "/event");
    }
}