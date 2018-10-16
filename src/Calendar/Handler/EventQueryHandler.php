<?php

namespace Calendar\Handler;

use Calendar\Query\EventQuery;
use React\Promise\Deferred;

class EventQueryHandler
{
    public function __invoke(EventQuery $query, Deferred $deffered)
    {
        $deffered->resolve(["dupa"]);
    }
}