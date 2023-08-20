<?php

namespace FoxWorn3365\YAMLPower;

final class VarParser {
    public static function get(object $vars, string $name) : mixed {
        if (@$vars->{$name} === null) {
            // Should be a string or an undefined var
            return $name;
        } else {
            // Should be a var
            return $vars->{$name};
        }
    }

    public static function methodizer(object $class, string $function, object $args, object $var, Error $error) : object {
        ArgChecker::check([
            'array args'
        ], $args, $error);

        if (method_exists($class, $function)) {
            switch (count(VarParser::get($var, $args->args))) {
                case 0:
                    $return = ($class->{$function})();
                    break;
                case 1:
                    $return = ($class->{$function})($args->args[0]);
                    break;
                case 2:
                    $return = ($class->{$function})($args->args[0], $args->args[1]);
                    break;
                case 3;
                    $return = ($class->{$function})($args->args[0], $args->args[1], $args->args[2]);
                    break;
                case 4:
                    $return = ($class->{$function})($args->args[0], $args->args[1], $args->args[2], $args->args[3]);
                    break;
                case 5:
                    $return = ($class->{$function})($args->args[0], $args->args[1], $args->args[2], $args->args[3], $args->args[4]);
                    break;
                case 6:
                    $return = ($class->{$function})($args->args[0], $args->args[1], $args->args[2], $args->args[3], $args->args[4], $args->args[5]);
                    break;
                default:
                    $error->throw('tooMuchArgsForClassFactoryOrExecutionException', true, ['We support a maximum of 7 args for a class method execution!']);
                    break;
            }

            if (ArgChecker::has('string to', $args)) {
                $var->{$args} = $return;
            }
        } else {
            $error->throw('methodInClassNotFoundException', true);
        }

        return $var;
    }

    public static function staticMethodizer(object $class, string $function, object $args, object $var, Error $error) : object {
        ArgChecker::check([
            'array args'
        ], $args, $error);

        if (method_exists($class, $function)) {
            switch (count(VarParser::get($var, $args->args))) {
                case 0:
                    $return = $class::{$function}();
                    break;
                case 1:
                    $return = $class::{$function}($args->args[0]);
                    break;
                case 2:
                    $return = $class::{$function}($args->args[0], $args->args[1]);
                    break;
                case 3;
                    $return = $class::{$function}($args->args[0], $args->args[1], $args->args[2]);
                    break;
                case 4:
                    $return = $class::{$function}($args->args[0], $args->args[1], $args->args[2], $args->args[3]);
                    break;
                case 5:
                    $return = $class::{$function}($args->args[0], $args->args[1], $args->args[2], $args->args[3], $args->args[4]);
                    break;
                case 6:
                    $return = $class::{$function}($args->args[0], $args->args[1], $args->args[2], $args->args[3], $args->args[4], $args->args[5]);
                    break;
                default:
                    $error->throw('tooMuchArgsForClassFactoryOrExecutionException', true, ['We support a maximum of 7 args for a class_static::method execution!']);
                    break;
            }

            if (ArgChecker::has('string to', $args)) {
                $var->{$args} = $return;
            }
        } else {
            $error->throw('methodInClassNotFoundException', true);
        }

        return $var;
    }
}