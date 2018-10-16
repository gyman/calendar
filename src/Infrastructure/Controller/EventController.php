<?php

namespace App\Controller;

use Calendar\Command\CreateEvent;
use Calendar\Event;
use Calendar\Event\TimeSpan;
use Calendar\Expression\Parser;
use Calendar\Query\EventQuery;
use Calendar\Repository\CalendarViewRepositoryInterface;
use Prooph\Bundle\ServiceBus\CommandBus;
use Prooph\Bundle\ServiceBus\QueryBus;
use Ramsey\Uuid\Uuid;
use React\Promise\Promise;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

class EventController extends AbstractController
{
    public function create(Request $request, CommandBus $bus, Uuid $calendarId) : Response
    {
        $bus->handle(CreateEvent::withData(
            $calendarId,
            Uuid::fromString($request->get("id")),
            $request->get("name"),
            Parser::fromString($request->get("expression")),
            TimeSpan::fromString($request->get("hours"))
        ));

        return new JsonResponse([], Response::HTTP_CREATED);
    }

    public function events(CalendarViewRepositoryInterface $repository, Uuid $calendarId) : Response
    {
        $calendar = $repository->find($calendarId);

        Assert::notNull($calendar);

        return new JsonResponse(array_map(function(Event $event) {
            return $event->toString();
        }, $calendar->events()));
    }

    public function search(Request $request, QueryBus $queryBus) : JsonResponse
    {
        $result = $queryBus->dispatch(
            EventQuery::fromRequest($request->request->all())
        )->done(function(array $result){
            return $result;
        }, function($reason){
            die(var_dump($reason));
        });

        die(var_dump($result));


//        return new JsonResponse(array_map(function(Event $event) {
//            return $event->toString();
//        }, $calendar->events()));
    }
}