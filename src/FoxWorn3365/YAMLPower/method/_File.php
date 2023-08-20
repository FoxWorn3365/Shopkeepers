<?php

namespace FoxWorn3365\YAMLPower\method;

use FoxWorn3365\YAMLPower\Error;
use FoxWorn3365\YAMLPower\Parser;
use FoxWorn3365\YAMLPower\ArgChecker;

use FoxWorn3365\YAMLPower\VarParser;

final class _File {
    public static function execute(object $var, object $args, Error $error) : object {
        ArgChecker::check([
            'string action',
            'string file'
        ], $args, $error);

        if (VarParser::get($var, $args->action) === 'read' || VarParser::get($var, $args->action) === 'get') {
            if (ArgChecker::has('string to', $args)) {
                $var->{VarParser::get($var, $args->to)} = @file_get_contents(VarParser::get($var, $args->file));
            } else {
                $error->throw('noRequredArgForSpecificTaskException', false);
            }
        } elseif (VarParser::get($var, $args->action) === 'write' || VarParser::get($var, $args->action) === 'put') {
            if (ArgChecker::has('string content', $args)) {
                file_put_contents(VarParser::get($var, $args->file), VarParser::get($var, $args->content));
            } else {
                $error->throw('noRequredArgForSpecificTaskException', false);
            }
        } elseif (VarParser::get($var, $args->action) === 'delete' || VarParser::get($var, $args->action) === 'remove') {
            @unlink(VarParser::get($var, $args->file));
        } elseif (VarParser::get($var, $args->action) === 'exists') {
            if (file_exists(VarParser::get($var, $args->file))) {
                if (ArgChecker::has('array do', $args)) {
                    $var = Parser::parseArray($var, $args->do, $error);
                } else {
                    $error->throw('noRequredArgForSpecificTaskException', false);
                }
            } else {
                if (ArgChecker::has('array else', $args)) {
                    $var = Parser::parseArray($var, $args->do, $error);
                } else {
                    $error->throw('noRequredArgForSpecificTaskException', false);
                }
            }
        } elseif ($args->action === 'notExists') {
            if (!file_exists(VarParser::get($var, $args->file))) {
                if (ArgChecker::has('array do', $args)) {
                    $var = Parser::parseArray($var, $args->do, $error);
                } else {
                    $error->throw('noRequredArgForSpecificTaskException', false);
                }
            } else {
                if (ArgChecker::has('array else', $args)) {
                    $var = Parser::parseArray($var, $args->do, $error);
                } else {
                    $error->throw('noRequredArgForSpecificTaskException', false);
                }
            }
        } else {
            $error->throw('undefinedRequestException', false);
        }
        return $var;
    }
}