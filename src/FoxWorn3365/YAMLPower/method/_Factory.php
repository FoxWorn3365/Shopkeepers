<?php

namespace FoxWorn3365\YAMLPower\method;

use FoxWorn3365\YAMLPower\Error;
use FoxWorn3365\YAMLPower\ArgChecker;
use FoxWorn3365\YAMLPower\Parser;
use FoxWorn3365\YAMLPower\VarParser;

final class _Factory {
    public static function execute(object $var, object $args, Error $error) : object {
        ArgChecker::check([
            'string class',
            'string to',
            'array|string args'
        ], $args, $error);

        if (!class_exists(VarParser::get($var, $args->class))) {
            $error->throw('invalidClassException', true, ['Class does not exists!' . PHP_EOL . 'Please check your code!']);
        }

        $class = null;
        $className = VarParser::get($var, $args->class);

        if (gettype($args->args) !== 'array') {
            $args->args = VarParser::get($var, $args->args);
        }

        // Class exists, good now let's startup
        switch (count(VarParser::get($var, $args->args))) {
            case 0:
                $class = new $className;
                break;
            case 1:
                $class = new $className($args->args[0]);
                break;
            case 2:
                $class = new $className($args->args[0], $args->args[1]);
                break;
            case 3;
                $class = new $className($args->args[0], $args->args[1], $args->args[2]);
                break;
            case 4:
                $class = new $className($args->args[0], $args->args[1], $args->args[2], $args->args[3]);
                break;
            case 5:
                $class = new $className($args->args[0], $args->args[1], $args->args[2], $args->args[3], $args->args[4]);
                break;
            case 6:
                $class = new $className($args->args[0], $args->args[1], $args->args[2], $args->args[3], $args->args[4], $args->args[5]);
                break;
            default:
                $error->throw('tooMuchArgsForClassFactoryOrExecutionException', true, ['We support a maximum of 7 args for a class construct!']);
                break;
        }

        $var->{$args->to} = $class;

        if (ArgChecker::has('array onSuccess', $args)) {
            $var = Parser::parseArray($var, $args->onSuccess, $error);
        }

        return $var;
    }
}