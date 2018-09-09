<?php

namespace App\Controller;

use App\Repository\CalendarRepository;
use Calendar\Calendar;
use Calendar\Command\CreateCalendar;
use Calendar\Repository\CalendarViewRepositoryInterface;
use League\Tactician\CommandBus;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

class CalendarController extends AbstractController
{
    public function create(Request $request, CommandBus $bus) : Response
    {
        $uuid = $request->get("id");
        $name = $request->get("name");

        Assert::uuid($uuid);
        Assert::notEmpty($name);

        $bus->handle(new CreateCalendar(Uuid::fromString($uuid), $name));

        return new JsonResponse([], Response::HTTP_CREATED);
    }

    public function getCalendar(Request $request, CalendarViewRepositoryInterface $repository) : Response
    {
        $uuid = $request->get("id");

        Assert::uuid($uuid);

        $calendar = $repository->findById(Uuid::fromString($uuid));

        Assert::notNull($calendar);

        return new JsonResponse($calendar->toArray(), Response::HTTP_CREATED);
    }
}