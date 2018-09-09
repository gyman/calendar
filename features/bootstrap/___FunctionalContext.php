<?php

use App\Kernel;
use App\Repository\CalendarViewRepository;
use App\Repository\EventViewRepository;
use Behat\Gherkin\Node\TableNode;
use Calendar\Command\AddEvent;
use Calendar\Event\TimeSpan;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Webmozart\Assert\Assert;

class FunctionalContext extends IntegrationContext
{
    /** @var Kernel */
    protected $kernel;

    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
        $this->calendarRepository = $kernel->getContainer()->get(CalendarViewRepository::class);
        $this->eventRepository = $kernel->getContainer()->get(EventViewRepository::class);
    }

    protected function get(string $serviceName) : ?object
    {
        return $this->kernel->getContainer()->get("test.service_container")->get($serviceName);
    }

    public function calendarRepositoryIsEmpty()
    {
        /** @var EntityManagerInterface $em */
        $em = $this->get('doctrine')->getManager();

        $metaData = $em->getMetadataFactory()->getAllMetadata();

        $tool = new SchemaTool($em);
        $tool->dropSchema($metaData);
        $tool->createSchema($metaData);
    }

    public function iAddToEvents(string $calendar, TableNode $table)
    {
        $calendar = $this->calendarRepository->findByName($calendar);

        Assert::notNull($calendar);

        $hash = $table->getHash();
        foreach ($hash as $row) {
            list($startDate, $endDate, $days) = $this->parseExpression($row['expression']);

            $this->get('tactician.commandbus.default')->handle(AddEvent::withData(
                $calendar->id(),
                $row['name'],
                $startDate ?? new DateTime,
                $endDate,
                TimeSpan::fromString($row['hours']),
                $days
            ));
        }
    }

    protected function parseExpression(string $expression) : array
    {
        $start = null;
        $end = null;
        $days = [];
        $tmp = [];

        if(preg_match("@after (\d{4}-\d{2}-\d{2})@", $expression, $tmp)) {
            $start = new DateTime($tmp[1]);
        }

        if(preg_match("@before (\d{4}-\d{2}-\d{2})@", $expression, $tmp)) {
            $end = new DateTime($tmp[1]);
        }

        if(preg_match_all("@(monday|tuesday|wednesday|thursday|friday|saturday|sunday)@", $expression, $tmp)) {
            $days = $tmp[0];
        }

        return [$start, $end, $days];
    }

}