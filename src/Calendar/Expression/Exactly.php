<?php

namespace Calendar\Expression;

use DateTime;
use Webmozart\Assert\Assert;

final class Exactly implements ExpressionInterface
{
    /** @var DateTime */
    private $date;

    protected function __construct(DateTime $dateTime)
    {
        $this->date = $dateTime;
    }

    public function isMatching(DateTime $date): bool
    {
        return $date->format("Ymd") == $date->format("Ymd");
    }

    public function __toString(): string
    {
        return "on " . $this->date->format("Y-m-d");
    }

    public static function fromString(string $expression): ExpressionInterface
    {
        return new self(new DateTime($expression));
    }
}