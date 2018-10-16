<?php

namespace Calendar\Query;

class EventQuery
{
    public static function fromRequest(array $all)
    {
        return new self();
    }
}