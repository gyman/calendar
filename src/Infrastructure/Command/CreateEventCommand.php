<?php

namespace App\Command;

use App\Repository\CalendarRepository;
use Calendar\Command\CreateCalendar;
use Calendar\Command\CreateEvent;
use Calendar\Event\TimeSpan;
use Calendar\Expression\Parser;
use DateTime;
use Prooph\Bundle\ServiceBus\CommandBus;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateEventCommand extends ContainerAwareCommand
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
            ->setName('calendar:event:create')
            ->addArgument('calendarId', InputArgument::REQUIRED, 'Id of calendar')
            ->addArgument('name', InputArgument::OPTIONAL, 'Event name', 'empty')
            ->addArgument('expression', InputArgument::OPTIONAL, 'Event expression', 'after ' . (new DateTime("now"))->format("Y-m-d"))
            ->addArgument('timespan', InputArgument::OPTIONAL, 'Event time span', null)
            ->addArgument('id', InputArgument::OPTIONAL, 'Event uuid', Uuid::uuid4())
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $calendarId = $input->getArgument('calendarId');

        $id = $input->getArgument('id');
        $name = $input->getArgument("name");

        $this->bus->dispatch(CreateEvent::withData(
            Uuid::fromString($calendarId),
            $id,
            $name,
            $input->getArgument("expression") != null ? Parser::fromString($input->getArgument("expression")) : null,
            $input->getArgument("timespan") != null ? TimeSpan::fromString($input->getArgument("timespan")) : null
        ));

        $output->writeln(sprintf('Event created'));
    }
}