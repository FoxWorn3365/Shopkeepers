<?php

namespace FoxWorn3365\YAMLPower\function;

use FoxWorn3365\YAMLPower\Error;

final class _Define {
    public static function execute(object $var, array $args, Error $error) : object { // Return value is the var value
        $string = implode(' ', $args);
        // SCHEMA: define a = 1, var a = 1 (CALL FROM OTHER)
        if ($args[0] === 'define' || $args[0] === 'var') {
            $varName = $args[1];
            if (strpos($varName, '=') !== false) {// Oh, shit, the value is inside the var (var a=1)
                $varValue = explode('=', $string)[1];
                $varName = explode('=', $varName)[0];
            } elseif ($args[2] == '=') {
                $varValue = explode(' = ', $string)[1];
            }
        } else {
            $error->throw('wrongCollectCallException', false);
        }
        $var->{$varName} = $varValue;
        return $var;
    }
}