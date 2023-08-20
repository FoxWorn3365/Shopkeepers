<?php

namespace FoxWorn3365\YAMLPower\function;

final class Index {
    public static array $list = [
        'define',
        'var',
        'print',
        'echo',
        'display',
        'dump',
        'include',
        'use',
        'replace'
    ];

    public static function classList() : array {
        $newlist = [];
        foreach (self::$list as $member) {
            $newlist[] = '_' . ucfirst($member);
        }
        return $newlist;
    }

    public static function getClass(string $member) : string {
        $class = '\FoxWorn3365\YAMLPower\function\_' . ucfirst($member);
        $class = new $class();
        return $class::class;
    }
}