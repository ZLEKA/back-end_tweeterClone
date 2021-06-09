<?php


class Tools
{
    public static function pascal_case_to_snake_case($str) {
        $parts = preg_replace('([A-Z])', ' $0', $str);
        $parts = strtolower(trim($parts));
        $parts = str_replace(' ', '_', $parts);
        return $parts;
    }

    public static function snake_case_to_pascal_case($str) {
        $parts = ucwords($str, '_');
        $parts = str_replace('_', '', $parts);
        return $parts;
    }

}