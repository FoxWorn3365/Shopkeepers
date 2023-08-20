<?php

namespace FoxWorn3365\YAMLPower\function;

use FoxWorn3365\YAMLPower\Error;

final class _Dump {
    public static function execute(object $var, array $args, ?Error $error = null) : object {
        if ($args[0] === 'dump') {
            var_dump($var->{$args[1]});
        }

        return $var;
    }
}