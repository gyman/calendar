<?php

namespace App\Type;

use Calendar\Expression\ExpressionInterface;
use Calendar\Expression\Parser;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class DateExpression extends Type
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return "TEXT";
    }

    public function getName() : string
    {
        return "date_expression";
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform) : string
    {
        return (string) $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform) : ?ExpressionInterface
    {
        return Parser::fromString($value);
    }
}