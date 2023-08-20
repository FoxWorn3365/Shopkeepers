<?php

namespace FoxWorn3365\YAMLPower;

use FoxWorn3365\YAMLPower\function\Index as FunctionIndex;
use FoxWorn3365\YAMLPower\method\Index as MethodIndex;

class Parser {
    protected string $content;
    protected object $parsed;
    public Error $error;

    public function __construct() {
        $this->error = new Error();
    }

    public function load(string $file) : self {
        $this->content = file_get_contents($file);
        $this->parsed = json_decode(json_encode(yaml_parse(($this->content))));
        return $this;
    }

    public function parse(string $key) : void {
        // the string is the output!
        $code = $this->parsed->{$key};
        if (gettype($code) !== 'array') {
            $this->error->throw('notArrayException', true, ['The code is not an array!']);
        } else {
            // Continue with the code!
            $var = new \stdClass;
            $var = self::parseArray($var, $code, $this->error);
            var_dump($var);
        }
    }

    public static function parseArray(object $var, array $code, Error $error) : object {
        $ext = null;
        foreach ($code as $row) {
            if (gettype($row) === 'string') {
                $args = explode(' ', $row);
                if (in_array($args[0], FunctionIndex::$list)) {
                    $var = FunctionIndex::getClass($args[0])::execute($var, $args, $error, $ext);
                }
            } elseif (gettype($row) === 'object') {
                foreach ($row as $loader => $data) {
                    if (in_array($loader, MethodIndex::$list)) {
                        $var = MethodIndex::getClass($loader)::execute($var, $data, $error);
                    } elseif (strpos($loader, '.') !== false) {
                        $object = explode('.', $loader);
                        if (in_array($object[0], MethodIndex::$list)) {
                            $data->action = $object[1];
                            $var = MethodIndex::getClass($object[0])::execute($var, $data, $error);
                        } elseif (@$var->{$object[0]} !== null) {
                            // Is a C.O., let's manage this!
                            if (gettype($var->{$object[0]}) === 'object') {
                                // Let's call the method using the methodizer
                                $var = VarParser::methodizer($var->{$object[0]}, $object[1], $data, $var, $error);
                            }
                        } elseif (in_array($loader, [])) {
                            $data->action = $object[1];
                            //$var = $ext->getMethod($object[0])->{$object[0] . '_executor'}($var, $data, $error);
                        }
                    } elseif (strpos($loader, ':') !== false) {
                        $object = explode(':', $loader);
                        // Is a static method created with other programs, let's parse with staticMethodizer!
                        $var = VarParser::staticMethodizer($var->{$object[0]}, $object[1], $data, $var, $error);
                    } elseif (in_array($loader, [])) {
                        //$var = $ext->getMethod($loader)->{$loader . '_executor'}($var, $data, $error);
                    }
                }
            }
        }

        return $var;
    }
}