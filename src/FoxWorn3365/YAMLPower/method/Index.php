<?php

namespace FoxWorn3365\YAMLPower\method;

final class Index {
    public static array $list = [
        'get',
        'post',
        'file',
        'dir',
        'json',
        'list',
        'object',
        'if',
        'for',
        'factory'
    ];

    public static function classList() : array {
        $newlist = [];
        foreach (self::$list as $member) {
            $newlist[] = ucfirst($member);
        }
        return $newlist;
    }

    public static function getClass(string $member) : string {
        $class = '\FoxWorn3365\YAMLPower\method\_' . ucfirst($member);
        $class = new $class();
        return $class::class;
    }
}