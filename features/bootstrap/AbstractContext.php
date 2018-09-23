<?php

use App\Kernel;
use App\Table;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Calendar\Calendar;
use Calendar\Event;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Prooph\EventStore\InMemoryEventStore;
use Prooph\EventStore\Stream;
use Prooph\EventStore\StreamName;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Webmozart\Assert\Assert;

abstract class AbstractContext implements Context
{

    /** @var Kernel */
    private $kernel;

    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;

        $inMemoryEventStore = $this->get(InMemoryEventStore::class);
        $inMemoryEventStore->create(new Stream(new StreamName(Table::EVENT_STREAM), new \ArrayIterator([])));

        $this->truncateTables();
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
        Assert::count($calendars, $count);
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
     * @Given /^calendar '([\d-]+)' has (\d+) events$/
     */
    public function calendarHasEvents(string $calendarId, int $eventsCount)
    {
        $events = $this->getEvents(Uuid::fromString($calendarId));

        Assert::eq(count($events), $eventsCount);
    }

    /**
     * @Given /^date \'([^\']*)\' matches event \'([^\']*)\' in calendar \'([^\']*)\'$/
     */
    public function dateMatchesEventInCalendar(string $date, string $eventName, string $id)
    {
        /** @var Calendar $calendar */
        $calendar = $this->getCalendarData(Uuid::fromString($id));

        /** @var Event[] $events */
        $events = $calendar->matchingEvents(new DateTime($date));

        Assert::count($events, 1);
        Assert::eq($events->first()->name(), $eventName);
    }

    /**
     * @When /^I add to calendar \'([^\']*)\' events:$/
     */
    public function iAddEventsToCalendar(string $calendarId, TableNode $table)
    {
        $hash = $table->getHash();

        foreach ($hash as $row) {
            $this->addEvent(Uuid::fromString($calendarId), $row['id'], $row['name'], $row['expression'], $row['hours']);
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

        $calendar = $this->getCalendarData($calendar);
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

    abstract protected function getCalendarData(UuidInterface $fromString) : array;

    abstract protected function getCalendars() : array;

    abstract protected function addEvent(string $calendarId, string $eventId, string $name, string $expression, string $hours);

    abstract protected function removeEvent(string $id, string $eventName);

    abstract protected function getEvents(UuidInterface $id) : array;

    protected function truncateTables()
    {
        /** @var EntityManagerInterface $em */
        $em = $this->get('doctrine')->getManager();

        $em->getConnection()->getSchemaManager()->dropAndCreateDatabase(
            $em->getConnection()->getDatabase()
        );

        $em->getConnection()->close();
        $em->getConnection()->connect();

        $tool = new SchemaTool($em);
        $tool->createSchema(
            $em->getMetadataFactory()->getAllMetadata()
        );

        if($em->getConnection()->getDatabasePlatform()->getName() !== "sqlite") {
            $projectDir = $this->kernel->getContainer()->getParameter("kernel.project_dir");

            $files = [
                $projectDir . "/vendor/prooph/pdo-event-store/scripts/mysql/01_event_streams_table.sql",
                $projectDir . "/vendor/prooph/pdo-event-store/scripts/mysql/02_projections_table.sql",
                $projectDir . "/vendor/prooph/pdo-snapshot-store/scripts/mysql_snapshot_table.sql",
            ];

            array_walk($files, function($path) use ($em) {
                $em->getConnection()->query(file_get_contents($path));
            });

//            $output = $this->runCommand('event-store:event-stream:create');
//            Assert::eq($output, "Event stream was created successfully.\n");
        }
    }

    /**
     * @Given /^calendar \'([^\']*)\' has data:$/
     */
    public function calendarHasData(string $calendarId, TableNode $table)
    {
        $calendarData = $this->getCalendarData(Uuid::fromString($calendarId));

        $hash = $table->getHash();

        Assert::eq($calendarData["id"], $hash[0]["id"]);
        Assert::eq($calendarData["name"], $hash[0]["name"]);
    }

    protected function runCommand(string $command, array $arguments = []) : string
    {
        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array_merge([
            'command' => $command
        ], $arguments));

        $output = new BufferedOutput();
        $exitCode = $application->run($input, $output);

        if(0 !== $exitCode)
        {
            $this->lastException = $output->fetch();
        }

        return $output->fetch();
    }

}
