<?php

use App\Kernel;
use App\Table;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Calendar\Calendar;
use Calendar\Event;
use Prooph\EventStore\InMemoryEventStore;
use Prooph\EventStore\Stream;
use Prooph\EventStore\StreamName;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Webmozart\Assert\Assert;

abstract class AbstractContext implements Context
{

    /** @var Kernel */
    private $kernel;

    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;

        $inMemoryEventStore = $this->get(InMemoryEventStore::class);
        $inMemoryEventStore->create(new Stream(new StreamName(Table::EVENT_STREAM_CALENDAR), new \ArrayIterator([])));
    }

    protected function get(string $serviceName) : object
    {
        return $this->kernel->getContainer()->get("test.service_container")->get($serviceName);
    }

    /**
     * @Given /^there is (\d+) calendars in calendar repository$/
     */
    public function thereIsCountOfCalendarsInRepository(int $count)
    {
        $calendars = $this->getCalendars();
        Assert::count(count($calendars), $count);
    }

    /**
     * @When /^I add new calendar with data:$/
     */
    public function iAddNewCalendar(TableNode $table)
    {
        foreach($table->getHash() as $calendarData) {
            $this->createCalendar($calendarData["id"], $calendarData["name"]);
        }
    }

    /**
     * @Given /^calendar "([^"]*)" has (\d+) events$/
     */
    public function calendarHasEvents(string $id, int $eventsCount)
    {
        $events = $this->getEvents(Uuid::fromString($id));

        Assert::eq(count($events), $eventsCount);
    }

    /**
     * @Given /^date \'([^\']*)\' matches event \'([^\']*)\' in calendar \'([^\']*)\'$/
     */
    public function dateMatchesEventInCalendar(string $date, string $eventName, string $id)
    {
        /** @var Calendar $calendar */
        $calendar = $this->getCalendar(Uuid::fromString($id));

        /** @var Event[] $events */
        $events = $calendar->matchingEvents(new DateTime($date));

        Assert::count($events, 1);
        Assert::eq($events->first()->name(), $eventName);
    }

    /**
     * @When /^I add to \'([^\']*)\' events:$/
     */
    public function iAddEventsToCalendar(string $id, TableNode $table)
    {
        $hash = $table->getHash();

        foreach ($hash as $row) {
            $this->addEvent(Uuid::fromString($id), $row['name'], $row['expression'], $row['hours']);
        }
    }

    /**
     * @Then /^I get (.*) events with (.*) occurrences for range from (.*) to (.*) in calendar \'([^\']*)\'$/
     */
    public function iGetEventsWithOccurrencesForRangeFromToInCalendar(int $eventsCount, int $occurrencesCount, string $dateFrom, string $dateTo, string $calendar)
    {
        $days = [];

        $dateFrom = new DateTime($dateFrom);
        $dateTo = new DateTime($dateTo);
        $dateTo->modify("+1 day");

        $calendar = $this->getCalendar($calendar);
        $period = new DatePeriod($dateFrom, new DateInterval('P1D'), $dateTo);

        $occurrences = $calendar->getOccurrences($dateFrom, $dateTo);

        foreach($period as $day) {
            $events = $calendar->matchingEvents($day);
            if(count($events) > 0) array_push($days, ...$events);
        }

        $events = array_unique($days);

        Assert::count($events, $eventsCount, sprintf("There should be %d events found but found %d", $eventsCount, count($events)));
        Assert::count($days, $occurrencesCount, sprintf("There should be %d days found but found %d", $occurrencesCount, count($days)));
        Assert::count($occurrences, $occurrencesCount, sprintf("There should be %d occurrences found but found %d", $occurrencesCount, count($occurrences)));
    }

    /**
     * @Given /^calendar repository is empty$/
     */
    public function calendarRepositoryIsEmpty()
    {
        Assert::count($this->getCalendars(), 0);
    }

    /**
     * @When /^I remove \'([^\']*)\' event from \'([^\']*)\' calendar$/
     */
    public function iRemoveEventFromCalendar(string $eventName, string $id)
    {
        $this->removeEvent($id, $eventName);
    }

    abstract protected function createCalendar(string $id, string $name);

    abstract protected function getCalendar(UuidInterface $fromString) : Calendar;

    abstract protected function getCalendars() : array;

    abstract protected function addEvent(UuidInterface $fromString, string $name, string $expression, string $hours);

    abstract protected function removeEvent(string $id, string $eventName);
}
