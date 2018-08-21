<?php

use App\Kernel;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Calendar\Calendar;
use Calendar\Event;
use Calendar\Expression\Parser;
use Calendar\Repository\CalendarViewRepositoryInterface;
use Ramsey\Uuid\Uuid;
use Test\Infrastructure\InMemoryCalendarRepository;
use Webmozart\Assert\Assert;

class IntegrationContext implements Context
{
    /** @var CalendarViewRepositoryInterface  */
    protected $calendarRepository;

    public function __construct(Kernel $kernel)
    {
        $this->calendarRepository = new InMemoryCalendarRepository();
    }

    /**
     * @Given /^there is (\d+) calendars in calendar repository$/
     */
    public function thereIsCalendarsInCalendarRepository(int $count)
    {
        Assert::count($this->calendarRepository->findAll(), $count);
    }

    /**
     * @When /^I add new \'([^\']*)\' calendar$/
     */
    public function iAddNewCalendar(string $name)
    {
        $calendar = new Calendar(Uuid::uuid4(), $name);

        $this->calendarRepository->save($calendar);
    }

    /**
     * @Given /^calendar \'([^\']*)\' has (\d+) events$/
     */
    public function calendarHasEvents(string $calendarName, int $eventsCount)
    {
        $calendar = $this->calendarRepository->findByName($calendarName);

        Assert::eq($calendar->count(), $eventsCount);
    }

    /**
     * @When /^I add to \'(.*)\' new event \'(.*)\' on \'(.*)\' at \'(.*)\'$/
     */
    public function iAddNewToNewEventOnAt(string $calendar, string $name, string $expression, string $hours)
    {
        $calendar = $this->calendarRepository->findByName($calendar);

        $calendar->addEvent(Event::create(Uuid::uuid4(), $calendar, $name, $expression, $hours));
    }

    /**
     * @Given /^date \'([^\']*)\' matches event \'([^\']*)\' in calendar \'([^\']*)\'$/
     */
    public function dateMatchesEventInCalendar(string $date, string $eventName, string $calendarName)
    {
        /** @var Calendar $calendar */
        $calendar = $this->calendarRepository->findByName($calendarName);

        Assert::notNull($calendar);

        /** @var Event[] $events */
        $events = $calendar->matchingEvents(new DateTime($date));

        Assert::count($events, 1);
        Assert::eq($events->first()->name(), $eventName);
    }

    /**
     * @When /^I add to \'([^\']*)\' events:$/
     */
    public function iAddToEvents(string $calendar, TableNode $table)
    {
        $calendar = $this->calendarRepository->findByName($calendar);

        $hash = $table->getHash();
        foreach ($hash as $row) {
            $calendar->addEvent(Event::create(Uuid::uuid4(), $calendar, $row['name'], $row['expression'], $row['hours']));
        }

        $this->calendarRepository->save($calendar);
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

        $calendar = $this->calendarRepository->findByName($calendar);
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
        Assert::count($this->calendarRepository->findAll()->toArray(), 0);
    }

    /**
     * @When /^I remove \'([^\']*)\' event from \'([^\']*)\' calendar$/
     */
    public function iRemoveEventFromCalendar(string $eventName, string $calendarName)
    {
        /** @var Calendar $calendar */
        $calendar = $this->calendarRepository->findByName($calendarName);
        $event = $calendar->getEventByName($eventName);
        $calendar->removeEvent($event);
    }

    /**
     * @When /^I update event \'([^\']*)\' in calendar \'([^\']*)\' with expression \'([^\']*)\'$/
     */
    public function iUpdateEventInCalendarWithExpression(string $eventName, string $calendarName, string $expression)
    {
        /** @var Calendar $calendar */
        $calendar = $this->calendarRepository->findByName($calendarName);
        /** @var Event $event */
        $event = $calendar->getEventByName($eventName);

        $event->updateExpression(Parser::fromString($expression));

        $this->calendarRepository->save($calendar);
    }
}
