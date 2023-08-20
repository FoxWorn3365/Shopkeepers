<?php

namespace FoxWorn3365\YAMLPower\method;

use FoxWorn3365\YAMLPower\Parser;
use FoxWorn3365\YAMLPower\ArgChecker;
use FoxWorn3365\YAMLPower\Error;

use FoxWorn3365\YAMLPower\VarParser;

final class _For {
    public static function execute(object $var, object $args, Error $error) : object {
        ArgChecker::check([
            'int index',
            'int|string max',
            'int increase',
            'array do',
            'string as'
        ], $args, $error);
        
        if (ArgChecker::gettype($args->max) == 'string') {
            $max = $var->{VarParser::get($var, $args->max)};
        } else {
            $max = $args->max;
        }

        for ($a = $args->index; $a < $max; $a = $a + $args->increase) {
            $var->{$args->as} = $a;
            $var = Parser::parseArray($var, $args->do, $error);
        }

        return $var;
    }
}