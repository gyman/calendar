<?php

namespace App\Command;

use App\Table;
use Prooph\EventStore\Pdo\MySqlEventStore;
use Prooph\EventStore\Stream;
use Prooph\EventStore\StreamName;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateEventStreamCommand extends ContainerAwareCommand
{
    private $eventStore;
    public function __construct(string $name = null, MySqlEventStore $eventStore)
    {
        parent::__construct($name);
        $this->eventStore = $eventStore;
    }
    protected function configure(): void
    {
        $this->setName('event-store:event-stream:create')
            ->setDescription('Create event_stream.')
            ->setHelp('This command creates the event_stream');
    }
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->eventStore->create(new Stream(new StreamName(Table::EVENT_STREAM), new \ArrayIterator([])));
        $output->writeln('<info>Event stream was created successfully.</info>');
    }
}