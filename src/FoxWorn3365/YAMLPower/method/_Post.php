<?php

namespace FoxWorn3365\YAMLPower\method;

use FoxWorn3365\YAMLPower\Error;
use FoxWorn3365\YAMLPower\Parser;
use FoxWorn3365\YAMLPower\ArgChecker;
use FoxWorn3365\YAMLPower\VarParser;

final class _Post {
    public static function execute(object $var, object $args, Error $error) : object {
        ArgChecker::check([
            'string url',
            'string content',
            'array headers',
            'string to'
        ], $args, $error);

        $content = VarParser::get($var, $args->content);

        if (gettype($content) === 'array') {
            $content = http_build_query($content);
        }

        $response = file_get_contents($args->url, false, stream_context_create(['http' => [
            'ignore_errors' => true,
            'header' => implode("\r\n", VarParser::get($var, $args->headers)),
            'method' => 'POST',
            'content' => $content
        ]]));

        $var->{$args->to} = $response;

        if (ArgChecker::has('string toHeader', $args)) {
            $var->{$args->toHeader} = $http_response_header;
        }

        /*
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
        */
        
        return $var;
    }
}