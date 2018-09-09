<?php

namespace Projection;

use App\Table;
use Calendar\Calendar;
use Calendar\DomainEvents\CalendarCreated;
use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Exception;
use PDO;
use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventStore\Projection\AbstractReadModel;
use Ramsey\Uuid\Uuid;
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

//        $this->em->clear(Calendar::class);
    }

    protected function onCalendarCreated(CalendarCreated $event): void
    {
        $result = $this->connection->insert(Table::READ_CALENDAR, [
            'id' => $event->id(),
            'name' => $event->name(),
            'created_at' => $event->createdAt()->format(self::DATE_FORMAT),
            'updated_at' => $event->createdAt()->format(self::DATE_FORMAT),
        ]);

        if (1 !== $result) {
            throw new Exception('Calendar was not inserted into read model!');
        }
    }
//
//    protected function onCreditWasMade(CreditWasMade $event): void
//    {
//        $accountId = $event->accountId();
//
//        $newBalance = $event->balance() + $event->amount();
//        $newAvailableBalance = $event->availableBalance() + $event->amount();
//
//        $this->connection->update(Table::ACCOUNT, [
//            'balance' => $newBalance,
//            'available_balance' => $newAvailableBalance,
//        ], ['id' => $accountId]);
//
//        $this->connection->insert(Table::CREDIT, [
//            'account_id' => $accountId,
//            'amount' => $event->amount(),
//            'balance_after' => $newBalance,
//            'created_at' => $event->createdAt()->format(self::DATE_FORMAT),
//            'updated_at' => $event->createdAt()->format(self::DATE_FORMAT),
//            'id' => $event->creditId()->toString(),
//        ]);
//    }
//
//    protected function onDebitWasMade(DebitWasMade $event): void
//    {
//        $accountId = $event->aggregateId();
//
//        $newBalance = $event->balance() - $event->amount();
//
//        $this->connection->update(Table::ACCOUNT, [
//            'balance' => $newBalance,
//            'available_balance' => $event->availableBalance(),
//            'updated_at' => $event->createdAt()->format(self::DATE_FORMAT),
//        ], ['id' => $accountId]);
//
//        $this->connection->insert(Table::DEBIT, [
//            'account_id' => $accountId,
//            'amount' => $event->amount(),
//            'balance_after' => $newBalance,
//            'created_at' => $event->createdAt()->format(self::DATE_FORMAT),
//            'updated_at' => $event->createdAt()->format(self::DATE_FORMAT),
//            'id' => $event->debitId()->toString(),
//        ]);
//
//        $newId = $event->newId();
//
//        if (null !== $newId) {
//            $lock = $this->connection->executeQuery(
//                sprintf(
//                    'SELECT l.amount FROM %s as l WHERE l.id = "%s" LIMIT 1',
//                    Table::LOCK,
//                    $event->lockId()->toString()
//                )
//            )->fetch(PDO::FETCH_ASSOC);
//
//            $newLockAmount = $lock['amount'] - $event->amount();
//
//            if ($newLockAmount > 0) {
//                $this->connection->insert(Table::LOCK, [
//                    'account_id' => $accountId,
//                    'amount' => $newLockAmount,
//                    'created_at' => $event->createdAt()->format(self::DATE_FORMAT),
//                    'updated_at' => $event->createdAt()->format(self::DATE_FORMAT),
//                    'id' => $newId,
//                ]);
//            }
//        }
//
//        $this->connection->delete(Table::LOCK, ['id' => $event->lockId()]);
//    }
//
//    protected function onFundsGotLocked(FundsGotLocked $event): void
//    {
//        $accountId = Uuid::fromString($event->aggregateId());
//
//        $newAvailableBalance = $event->availableBalance() - $event->amount();
//
//        $this->connection->update(Table::ACCOUNT, [
//            'available_balance' => $newAvailableBalance,
//            'updated_at' => (new DateTime())->format(self::DATE_FORMAT),
//        ], ['id' => $accountId]);
//
//        $this->connection->insert(Table::LOCK, [
//            'account_id' => $accountId,
//            'amount' => $event->amount(),
//            'created_at' => $event->createdAt()->format(self::DATE_FORMAT),
//            'updated_at' => $event->createdAt()->format(self::DATE_FORMAT),
//            'id' => $event->lockId()->toString(),
//        ]);
//    }
//
//    protected function onFundsGotUnlocked(FundsGotUnlocked $event): void
//    {
//        $accountId = Uuid::fromString($event->aggregateId());
//
//        $this->connection->update(Table::ACCOUNT, [
//            'balance' => $event->balance(),
//            'available_balance' => $event->availableBalance(),
//            //            'updated_at' => $event->createdAt()->format(self::DATE_FORMAT)
//        ], ['id' => $accountId]);
//
//        $this->connection->delete(Table::LOCK, [
//            'id' => $event->lockId()->toString(),
//        ]);
//    }
}
