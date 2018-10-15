<?php

namespace App\Type;

use Calendar\Event\TimeSpan as TimeSpanVO;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class TimeSpan extends Type
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return "CHAR(20)";
    }

    public function getName() : string
    {
        return "timespan";
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform) : string
    {
        return (string) $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform) : ?TimeSpanVO
    {
        return TimeSpanVO::fromString($value);
    }
}