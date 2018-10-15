<?php

namespace App\Controller;

use Calendar\Calendar;
use Calendar\Command\CreateCalendar;
use Calendar\Repository\CalendarViewRepositoryInterface;
use Prooph\Bundle\ServiceBus\CommandBus;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

class CalendarController extends AbstractController
{
    public function create(CommandBus $bus, Uuid $calendarId, string $name) : Response
    {
        $bus->dispatch(new CreateCalendar($calendarId, $name));

        return new JsonResponse([], Response::HTTP_CREATED);
    }

    public function getCalendar(CalendarViewRepositoryInterface $repository, Uuid $calendarId) : Response
    {
        $calendar = $repository->find($calendarId);

        Assert::notNull($calendar);

        return new JsonResponse($calendar->toArray(), Response::HTTP_CREATED);
    }

    public function list(CalendarViewRepositoryInterface $repository) : Response
    {
        $calendars = $repository->findAll();

        return new JsonResponse(array_map(function(Calendar $calendar) : array {
            return $calendar->toArray();
        }, $calendars), Response::HTTP_CREATED);
    }
}