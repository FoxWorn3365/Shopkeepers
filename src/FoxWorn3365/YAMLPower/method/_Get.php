<?php

namespace FoxWorn3365\YAMLPower\method;

use FoxWorn3365\YAMLPower\Error;
use FoxWorn3365\YAMLPower\Parser;
use FoxWorn3365\YAMLPower\ArgChecker;

use FoxWorn3365\YAMLPower\VarParser;

final class _Get {
    public static function execute(object $var, object $args, Error $error) : object {
        ArgChecker::check([
            'string url',
            'string to'
        ], $args, $error);

        $response = file_get_contents(VarParser::get($var, $args->url), false, stream_context_create(['http' => ['ignore_errors' => true]]));
        $var->{VarParser::get($var, $args->to)} = $response;

        if (ArgChecker::has('string toResponseHeader', $args)) {
            $var->{VarParser::get($var, $args->toResponseHeader)} = $http_response_header;
        }

        if (ArgChecker::has('array onError', $args)) {
            if (@$http_response_header['status'] !== null && strpos($http_response_header['status'], '20') === false) {
                $var = Parser::parseArray($var, $args->onError, $error);
            }
        }

        if (ArgChecker::has('array onSuccess', $args)) {
            if (@$http_response_header['status'] !== null && strpos($http_response_header['status'], '20') !== false) {
                $var = Parser::parseArray($var, $args->onSuccess, $error);
            }
        }
        return $var;
    }
}