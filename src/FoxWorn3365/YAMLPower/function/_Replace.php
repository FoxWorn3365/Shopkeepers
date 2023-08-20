<?php

namespace FoxWorn3365\YAMLPower\function;

// CUSTOM

final class _Replace {
    public static function execute(object $var, array $args) : object {
        if (@$var->{$args[1]} !== null) {
            $string = $var->{$args[1]};
            foreach ($var as $name => $value) {
                $string = str_replace($value, '{'.$name.'}', $string);
            }
            $var->{$args[1]} = $string;
        }

        return $var;
    }
}