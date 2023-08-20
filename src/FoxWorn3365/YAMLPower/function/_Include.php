<?php

namespace FoxWorn3365\YAMLPower\function;

use FoxWorn3365\YAMLPower\Error;

final class _Include {
    public static function execute(object $var, array $args, Error $error) : object {
        if ($args[0] === 'include') {
            include $args[1];
        }
        return $var;
    }
}