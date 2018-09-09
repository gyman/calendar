<?php

namespace Projection;

use App\Table;
use Prooph\Bundle\EventStore\Projection\ReadModelProjection;
use Prooph\Common\Messaging\Message;
use Prooph\EventStore\Projection\ReadModelProjector;

class CalendarProjection implements ReadModelProjection
{
    public function project(ReadModelProjector $projector): ReadModelProjector
    {
        $projector->fromStream(Table::EVENT_STREAM)
            ->whenAny(function ($state, Message $event): void {
                /** @var CalendarReadModel $readModel */
                $readModel = $this->readModel();
                $readModel($event);
            });

        return $projector;
    }
}
