<?php

namespace FoxWorn3365\YAMLPower\method;

use FoxWorn3365\YAMLPower\Error;
use FoxWorn3365\YAMLPower\Parser;
use FoxWorn3365\YAMLPower\ArgChecker;

use FoxWorn3365\YAMLPower\VarParser;

final class _Object {
    public static function execute(object $var, object $args, Error $error) : object {
        ArgChecker::check([
            'string action'
        ], $args, $error);

        if (VarParser::get($var, $args->action) === 'make' || VarParser::get($var, $args->action) === 'create') {
            if (ArgChecker::has('string to', $args)) {
                $var->{$args->to} = new \stdClass;
            } else {
                $error->throw('noRequredArgForSpecificTaskException', true);
            }
        } elseif (VarParser::get($var, $args->action) === 'set') {
            if (ArgChecker::has('string object', $args) && ArgChecker::has('string key', $args) && ArgChecker::has('string value', $args)) {
                $var->{$args->object}->{VarParser::get($var, $args->key)} = VarParser::get($var, $args->value);
            } else {
                $error->throw('noRequredArgForSpecificTaskException', true);
            }
        } elseif (VarParser::get($var, $args->action) === 'get') {
            if (ArgChecker::has('string object', $args) && ArgChecker::has('string key', $args) && ArgChecker::has('string to', $args)) {
                $var->{$args->to} = $var->{$args->object}->{VarParser::get($var, $args->key)};
            } else {
                $error->throw('noRequredArgForSpecificTaskException', true);
            }
        } elseif (VarParser::get($var, $args->action) === 'count') {
            if (ArgChecker::has('string object', $args) && ArgChecker::has('string to', $args)) {
                $count = 0;
                foreach ($var->{$args->object} as $value) {
                    $count++;
                }
                $var->{$args->to} = $count;
            } else {
                $error->throw('noRequredArgForSpecificTaskException', true);
            }
        } elseif ($args->action === 'foreach') {
            if (ArgChecker::has('string object', $args) && ArgChecker::has('string as', $args) && ArgChecker::has('array do', $args)) {
                foreach ($var->{$args->object} as $key => $value) {
                    if (strpos($args->as, ' => ') !== false) {
                        $var->{explode(' => ', $args->as)[0]} = $key;
                        $var->{explode(' => ', $args->as)[1]} = $value;
                        $var = Parser::parseArray($var, $args->do, $error);
                    } else {
                        $var->{$args->as} = $value;
                        $var = Parser::parseArray($var, $args->do, $error);
                    }
                }
            } else {
                $error->throw('noRequredArgForSpecificTaskException', true);
            }
        } else {
            $error->throw('undefinedRequestException', false);
        }

        return $var;
    }
}