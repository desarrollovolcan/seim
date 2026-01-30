<?php

class Validator
{
    public static function required(string $value): bool
    {
        return trim($value) !== '';
    }

    public static function email(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function optionalEmail(?string $value): bool
    {
        $value = trim((string)$value);
        if ($value === '') {
            return true;
        }
        return self::email($value);
    }

    public static function url(?string $value): bool
    {
        $value = trim((string)$value);
        if ($value === '') {
            return true;
        }
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    public static function rut(?string $value): bool
    {
        $value = normalize_rut($value);
        if ($value === '') {
            return true;
        }
        return is_valid_rut($value);
    }
}
