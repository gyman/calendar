<?php
namespace Calendar;

use Carbon\Carbon;
use DateTime;
use DateTimeImmutable;

function cartesian_product(array $set) : array
{
    if (!$set) {
        return array(array());
    }
    $subset = array_shift($set);
    $cartesianSubset = cartesian_product($set);
    $result = array();
    foreach ($subset as $value) {
        foreach ($cartesianSubset as $p) {
            array_unshift($p, $value);
            $result[] = $p;
        }
    }
    return $result;
}

function get_class_last_part($object): string
{
    if (!is_object($object)) {
        throw new \InvalidArgumentException('Argument #0 must be an object');
    }

    return array_slice(explode('\\', get_class($object)), -1)[0];
}

function carbonite(DateTimeImmutable $dateTimeImmutable): Carbon
{
    $dateTime = new DateTime(null, $dateTimeImmutable->getTimezone());
    $dateTime->setTimestamp($dateTimeImmutable->getTimestamp());

    return Carbon::instance($dateTime);
}