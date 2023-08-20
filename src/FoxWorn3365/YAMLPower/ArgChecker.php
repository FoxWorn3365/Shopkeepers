<?php

namespace FoxWorn3365\YAMLPower;

final class ArgChecker {
    public static function check(array $args, object $data, Error $error) : void {
        $arg = [];
        foreach ($args as $member) {
            if (strpos($member, ' ') !== false) {
                // Also the type is defined soo...
                if (strpos($member, '|') !== false) {
                    $args[explode(' ', $member)[1]] = explode('|', explode(' ', $member)[0]);
                } else {
                    $args[explode(' ', $member)[1]] = [explode(' ', $member)[0]];
                }
            } else {
                $args[$member] = null;
            }
        }

        // Now compare
        foreach ($arg as $member => $type) {
            if (@$data->{$member} !== null) {
                // Go!
                if ($type !== null && !in_array(self::gettype($data->member), $type)) {
                    // Error
                    $error->throw('wrongTypeException', true);
                }
            } else {
                $error->throw('notAllArgsException', true);
            }
        }

        // It's survived so yeee
    }

    public static function has(string $arg, object $data) : bool {
        if (strpos($arg, ' ') !== false) {
            // has the arg
            if (@$data->{explode(' ', $arg)[1]} !== null && self::gettype($data->{explode(' ', $arg)[1]}) === explode(' ', $arg)[0]) {
                return true;
            }
        } else {
            if (@$data->{$arg} !== null) {
                return true;
            }
        }
        return false;
    }

    public static function gettype(mixed $var) : string {
        $translator = [
            'integer' => 'int',
            'string' => 'string',
            'boolean' => 'bool',
            'double' => 'float',
            'array' => 'array',
            'object' => 'object',
            'resource' => 'resource',
            'NULL' => 'NULL'
        ];
        return @$translator[gettype($var)];
    }
}