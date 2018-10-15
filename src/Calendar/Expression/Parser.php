<?php

namespace Calendar\Expression;

class Parser
{
    public static function fromString(?string $string = "") : ?ExpressionInterface
    {
        if($string == null) {
            return null;
        }

        $lexer = new DateExpressionLexer();
        $stream = $lexer->lex($string);

        $gramma = new DateExpressionGrammar();
        $parser = new DateExpressionParser($gramma);

        /** @var ExpressionInterface $result */
        return $parser->parse($stream);
    }
}