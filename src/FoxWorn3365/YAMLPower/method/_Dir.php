<?php

namespace FoxWorn3365\YAMLPower\method;

use FoxWorn3365\YAMLPower\Error;
use FoxWorn3365\YAMLPower\Parser;
use FoxWorn3365\YAMLPower\ArgChecker;

use FoxWorn3365\YAMLPower\VarParser;

final class _Dir {
    public static function execute(object $var, object $args, Error $error) : object {
        ArgChecker::check([
            'string action',
            'string dir'
        ], $args, $error);

        if (VarParser::get($var, $args->action) === 'create' || VarParser::get($var, $args->action) === 'make') {
            if (ArgChecker::has('int permissions', $args)) {
                $permissions = VarParser::get($var, $args->permissions);
            } else {
                $permissions = 0777;
            }

            if (ArgChecker::has('bool recursive', $args)) {
                $recursive = VarParser::get($var, $args->recursive);
            } else {
                $recursive = false;
            }

            @mkdir(VarParser::get($var, $args->dir), $permissions, $recursive);
        } elseif (VarParser::get($var, $args->action) === 'remove' || VarParser::get($var, $args->action) === 'delete') {
            self::rrmdir(VarParser::get($var, $args->dir));
        } elseif (VarParser::get($var, $args->action) === 'exists') {
            if (is_dir(VarParser::get($var, $args->file))) {
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
            $error->throw('undefinedRequestException');
        }
        
        return $var;
    }

    protected static function rrmdir($dir) { 
        if (is_dir($dir)) { 
          $objects = scandir($dir);
          foreach ($objects as $object) { 
            if ($object != "." && $object != "..") { 
              if (is_dir($dir. DIRECTORY_SEPARATOR .$object) && !is_link($dir."/".$object))
                self::rrmdir($dir. DIRECTORY_SEPARATOR .$object);
              else
                unlink($dir. DIRECTORY_SEPARATOR .$object); 
            } 
          }
          rmdir($dir); 
        } 
    }
}