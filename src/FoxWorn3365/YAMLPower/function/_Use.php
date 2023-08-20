<?php

namespace FoxWorn3365\YAMLPower\function;

use FoxWorn3365\YAMLPower\Error;
use FoxWorn3365\YAMLPower\Extension;

final class _Use {
    public static function execute(object $var, array $args, Error $error, Extension $ext) : object {
        $ext->load($args[1]);
        $var->__extPrivateOBJ = $ext;
        return $var;
    }
}