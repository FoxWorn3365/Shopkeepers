<?php

namespace FoxWorn3365\YAMLPower\function;

use FoxWorn3365\YAMLPower\Error;

final class _Print {
    public static function execute(object $var, array $args, Error $error) : object {
        if ($args[0] === 'print' || $args[0] === 'display' || $args[0] === 'echo') {
            if (@$var->{$args[1]} === null) {
                $error->throw('notAllArgsException', true);
            } else {
                if (gettype($var->{$args[1]}) === 'string') {
                    //echo $var->{$args[1]};
                } else {
                    $error->throw('wrongTypeException', true);
                }
            }
        }
        return $var;
    }
}