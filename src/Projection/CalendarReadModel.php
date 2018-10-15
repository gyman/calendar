<?php

namespace Projection;

use App\Table;
use Calendar\DomainEvents\CalendarCreated;
use Calendar\DomainEvents\EventCreated;
use Calendar\View\CalendarView;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventStore\Projection\AbstractReadModel;
use function Calendar\get_class_last_part;

class CalendarReadModel extends AbstractReadModel
{
    const DATE_FORMAT = 'Y-m-d H:i:s';

    /** @var Connection */
    private $connection;

    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->connection = $em->getConnection();
    }

    public function init(): void
    {
        $tool = new SchemaTool($this->em);

        $tool->createSchema(
            $this->em->getMetadataFactory()->getAllMetadata()
        );
    }

    public function isInitialized(): bool
    {
        $tool = new SchemaTool($this->em);

        $sql = $tool->getUpdateSchemaSql(
            $this->em->getMetadataFactory()->getAllMetadata(),
            true
        );

        return 0 === count($sql);
    }

    public function reset(): void
    {
        $tool = new SchemaTool($this->em);
        $tool->dropSchema(
            $this->em->getMetadataFactory()->getAllMetadata()
        );

        $tool->createSchema(
            $this->em->getMetadataFactory()->getAllMetadata()
        );
    }

    public function delete(): void
    {
        $tool = new SchemaTool($this->em);
        $tool->dropSchema(
            $this->em->getMetadataFactory()->getAllMetadata()
        );
    }

    public function __invoke(AggregateChanged $event): void
    {
        $method = 'on' . get_class_last_part($event);
        $this->$method($event);
        $this->em->clear(CalendarView::class);
    }

    protected function onCalendarCreated(CalendarCreated $event): void
    {
        $this->connection->insert(Table::READ_CALENDAR, [
            'id' => $event->id(),
            'name' => $event->name(),
//            'created_at' => $event->createdAt()->format(self::DATE_FORMAT),
//            'updated_at' => $event->createdAt()->format(self::DATE_FORMAT),
        ]);
    }

    protected function onEventCreated(EventCreated $event): void
    {
        $this->connection->insert(Table::READ_EVENT, [
            'event_id' => $event->id(),
            'calendar_id' => $event->aggregateId(),
            'name' => $event->name(),
            'expression' => (string) $event->expression(),
            'timespan' => (string) $event->timespan()
        ]);
    }

}
