<?php

namespace App\Command;

use Calendar\Command\CreateCalendar;
use Prooph\Bundle\ServiceBus\CommandBus;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateCalendarCommand extends ContainerAwareCommand
{
    /** @var CommandBus */
    private $bus;

    public function __construct(string $name = null, CommandBus $bus)
    {
        parent::__construct($name);
        $this->bus = $bus;
    }
    protected function configure(): void
    {
        $this
            ->setName('calendar:create')
            ->addArgument('name', InputArgument::OPTIONAL, 'Calendar name', '')
            ->addArgument('id', InputArgument::OPTIONAL, 'Calendar uuid', Uuid::uuid4()->toString())
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $calendarId = $input->getArgument('id');
        $calendarName = $input->getArgument("name");

        $this->bus->dispatch(new CreateCalendar(Uuid::fromString($calendarId), $calendarName));
        $output->writeln(sprintf('Calendar \'%s\' created with id \'%s\'', $calendarName, $calendarId));
    }
}