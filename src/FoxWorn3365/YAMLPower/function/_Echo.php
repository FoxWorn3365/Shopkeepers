<?php

namespace FoxWorn3365\YAMLPower\function;

use FoxWorn3365\YAMLPower\Error;

final class _Echo {
    public static function execute(object $var, array $args, Error $error) : object {
        return _Print::execute($var, $args, $error);
    }
}