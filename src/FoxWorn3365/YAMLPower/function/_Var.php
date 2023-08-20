<?php

namespace FoxWorn3365\YAMLPower\function;

use FoxWorn3365\YAMLPower\Error;

final class _Var {
    public static function execute(object $var, array $args, Error $error) : object {
        return _Define::execute($var, $args, $error);
    }
}