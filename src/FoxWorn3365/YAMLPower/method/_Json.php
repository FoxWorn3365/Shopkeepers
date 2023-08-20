<?php

namespace FoxWorn3365\YAMLPower\method;

use FoxWorn3365\YAMLPower\Error;
use FoxWorn3365\YAMLPower\ArgChecker;

use FoxWorn3365\YAMLPower\VarParser;

final class _Json {
    public static function execute(object $var, object $args, Error $error) : object {
        ArgChecker::check([
            'string action',
            'string from',
            'string to'
        ], $args, $error);

        if (VarParser::get($var, $args->action) === 'serialize' || VarParser::get($var, $args->action) === 'encode') {
            $var->{$args->to} = json_encode(VarParser::get($var, $args->from));
        } elseif (VarParser::get($var, $args->action) === 'deserialize' || VarParser::get($var, $args->action) === 'decode' || VarParser::get($var, $args->action) === 'parse') {
            $var->{$args->to} = json_decode(VarParser::get($var, $args->from));
        } else {
            $error->throw('undefinedRequestException', false);
        }
        return $var;
    }
}