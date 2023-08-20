<?php

namespace FoxWorn3365\YAMLPower\method;

use FoxWorn3365\YAMLPower\Error;
use FoxWorn3365\YAMLPower\ArgChecker;
use FoxWorn3365\YAMLPower\Parser;

use FoxWorn3365\YAMLPower\VarParser;

final class _If {
    public static function execute(object $var, object $args, Error $error) : object {
        ArgChecker::check([
            'string first',
            'string comparator',
            'string second',
            'array do'
        ], $args, $error);

        $a = @$var->{$args->first};
        $b = @$var->{VarParser::get($var, $args->second)};

        switch ($args->comparator) {
            case '==':
                if ($a == $b) {
                    $var = Parser::parseArray($var, $args->do, $error);
                } else {
                    if (ArgChecker::has('array else', $args)) {
                        $var = Parser::parseArray($var, $args->else, $error);
                    }
                }
                break;
            case '===':
                if ($a === $b) {
                    $var = Parser::parseArray($var, $args->do, $error);
                } else {
                    if (ArgChecker::has('array else', $args)) {
                        $var = Parser::parseArray($var, $args->else, $error);
                    }
                }
                break;
            case '!=':
                if ($a != $b) {
                    $var = Parser::parseArray($var, $args->do, $error);
                } else {
                    if (ArgChecker::has('array else', $args)) {
                        $var = Parser::parseArray($var, $args->else, $error);
                    }
                }
                break;
            case '!==':
                if ($a !== $b) {
                    $var = Parser::parseArray($var, $args->do, $error);
                } else {
                    if (ArgChecker::has('array else', $args)) {
                        $var = Parser::parseArray($var, $args->else, $error);
                    }
                }
                break;
            case '<':
                if ($a < $b) {
                    $var = Parser::parseArray($var, $args->do, $error);
                } else {
                    if (ArgChecker::has('array else', $args)) {
                        $var = Parser::parseArray($var, $args->else, $error);
                    }
                }
                break;
            case '>':
                if ($a > $b) {
                    $var = Parser::parseArray($var, $args->do, $error);
                } else {
                    if (ArgChecker::has('array else', $args)) {
                        $var = Parser::parseArray($var, $args->else, $error);
                    }
                }
                break;
            case '>=':
                if ($a !== $b) {
                    $var = Parser::parseArray($var, $args->do, $error);
                } else {
                    if (ArgChecker::has('array else', $args)) {
                        $var = Parser::parseArray($var, $args->else, $error);
                    }
                }
                break;
            case '<=':
                if ($a !== $b) {
                    $var = Parser::parseArray($var, $args->do, $error);
                } else {
                    if (ArgChecker::has('array else', $args)) {
                        $var = Parser::parseArray($var, $args->else, $error);
                    }
                }
                break;
            case 'is':
                $b = $args->second;
                if ($b == 'empty') {
                    if (empty($a)) {
                        $var = Parser::parseArray($var, $args->do, $error);
                    } else {
                        if (ArgChecker::has('array else', $args)) {
                            $var = Parser::parseArray($var, $args->else, $error);
                        }
                    }
                } elseif ($b == 'null') {
                    if ($a === null) {
                        $var = Parser::parseArray($var, $args->do, $error);
                    } else {
                        if (ArgChecker::has('array else', $args)) {
                            $var = Parser::parseArray($var, $args->else, $error);
                        }
                    }
                } elseif ($b == 'true') {
                    if ($a) {
                        $var = Parser::parseArray($var, $args->do, $error);
                    } else {
                        if (ArgChecker::has('array else', $args)) {
                            $var = Parser::parseArray($var, $args->else, $error);
                        }
                    }
                } elseif ($b == 'false') {
                    if (!$a) {
                        $var = Parser::parseArray($var, $args->do, $error);
                    } else {
                        if (ArgChecker::has('array else', $args)) {
                            $var = Parser::parseArray($var, $args->else, $error);
                        }
                    }
                }
                break;
            case 'not':
                $b = $args->second;
                if ($b == 'empty') {
                    if (!empty($a)) {
                        $var = Parser::parseArray($var, $args->do, $error);
                    } else {
                        if (ArgChecker::has('array else', $args)) {
                            $var = Parser::parseArray($var, $args->else, $error);
                        }
                    }
                } elseif ($b == 'null') {
                    if ($a !== null) {
                        $var = Parser::parseArray($var, $args->do, $error);
                    } else {
                        if (ArgChecker::has('array else', $args)) {
                            $var = Parser::parseArray($var, $args->else, $error);
                        }
                    }
                } elseif ($b == 'true') {
                    if (!$a) {
                        $var = Parser::parseArray($var, $args->do, $error);
                    } else {
                        if (ArgChecker::has('array else', $args)) {
                            $var = Parser::parseArray($var, $args->else, $error);
                        }
                    }
                } elseif ($b == 'false') {
                    if ($a) {
                        $var = Parser::parseArray($var, $args->do, $error);
                    } else {
                        if (ArgChecker::has('array else', $args)) {
                            $var = Parser::parseArray($var, $args->else, $error);
                        }
                    }
                }
                break;
        }

        return $var;
    }
}