<?php

namespace App\Config;

class Validator
{
    public static function validateForm($fields)
    {
        foreach ($fields as $index => $value) {
            $value = strip_tags(trim($value));
            $fields[$index] = $value;
        }
        return $fields;
    }
}
