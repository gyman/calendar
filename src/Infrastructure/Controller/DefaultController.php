<?php

namespace App\Controller;

use Calendar\Calendar;
use Calendar\Command\CreateEvent;
use Calendar\Command\UpdateEvent;
use Calendar\Event\TimeSpan;
use Calendar\Repository\CalendarRepositoryInterface;
use Calendar\Repository\EventRepositoryInterface;
use Carbon\Carbon;
use DateTime;
use League\Tactician\CommandBus;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

class DefaultController extends AbstractController
{
    public function indexAction(Request $request, CalendarRepositoryInterface $calendarRepository) : Response
    {
        $calendars = $calendarRepository->findAll();
        $startDate = new DateTime("last monday");
        $endDate = clone $startDate;
        $endDate->modify("+7 days");

        $events = [];

        /** @var Calendar $calendar */
        foreach($calendars as $calendar) {
            $result = $calendar->getOccurrences($startDate, new DateTime("2018-03-31"));
            if(count($result) === 0) continue;
            array_push($events, ...$result);
        }

        return $this->render("default/index.html.twig", [
            "events" => $events,
            "startDate" => $startDate,
            "endDate" => $endDate
        ]);
    }

    public function addEventAction(Request $request, CommandBus $bus, CalendarRepositoryInterface $repository) : Response
    {
        $calendars = array_reduce($repository->findAll(), function(&$result, Calendar $calendar) : array {
            $result[$calendar->name()] = $calendar->id()->toString();
            return $result;
        }, []);

        Assert::minCount($calendars, 1);

        $calendarId = Uuid::fromString(reset($calendars));

        $command = CreateEvent::withData(
            $calendarId,
            "",
            Carbon::createFromFormat('YmdHi', $request->get('startDate', (new DateTime('now'))->format('YmdHi'))),
            null,
            TimeSpan::fromDateTimes(
                Carbon::createFromFormat('YmdHi', $request->get('startDate', (new DateTime('now'))->format('YmdHi'))),
                Carbon::createFromFormat('YmdHi', $request->get('endDate', (new DateTime('+1 hour'))->format('YmdHi')))
            ),
            [
                Carbon::createFromFormat('YmdHi', $request->get('startDate', (new DateTime('now'))->format('YmdHi')))->format('N'),
            ]
        );

        $form = $this->createForm(CreateEventType::class, $command, ["calendars" => $calendars]);

        if($request->isMethod("POST")) {
            $form->handleRequest($request);

            if($form->isValid()) {
                $bus->handle($form->getData());
            }
        }

        return $this->render("default/addEvent.html.twig", [
            "form" => $form->createView()
        ]);
    }

    public function getEventsAction(Request $request, CalendarRepositoryInterface $repository) : JsonResponse
    {
        $start = Carbon::parse($request->get('start', 'this week'));
        $end = Carbon::parse($request->get('end', 'next week'));

        $start->modify("00:00:00");
        $end->modify("23:59:59");

        $events = [];

        /** @var Calendar $calendar */
        foreach ($repository->findAll() as $calendar) {
            $occurrences = $calendar->getOccurrences($start, $end);
            if(count($occurrences) === 0) continue;
            array_push($events, ...$occurrences);
        }

        return new JsonResponse($events);
    }

    public function updateEventAction(Request $request, CommandBus $bus, CalendarRepositoryInterface $calendarRepository, EventRepositoryInterface $eventRepository) : Response
    {
        $event = $eventRepository->findById(Uuid::fromString($request->get('eventId')));
        Assert::notNull($event);

        $calendars = array_reduce($calendarRepository->findAll(), function(&$result, Calendar $calendar) : array {
            $result[$calendar->name()] = $calendar->id()->toString();
            return $result;
        }, []);

        $command = UpdateEvent::withData();

        $form = $this->createForm(UpdateEventType::class, $command, ["calendars" => $calendars]);

        if($request->isMethod("POST")) {
            $form->handleRequest($request);

            if($form->isValid()) {
                $bus->handle($form->getData());
            }
        }

        return $this->render("default/updateEvent.html.twig", [
            "form" => $form->createView()
        ]);
    }




}