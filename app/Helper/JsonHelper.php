<?php

namespace App\Helper;


class JsonHelper
{

    public static function encode(array $data): string
    {
        return (string)json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public static function decode(string $data): object
    {
        return (object)json_decode($data, true);
    }

    public static function decodeArray(string $data): array
    {
        return (array)json_decode($data, true);
    }

    public static function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

}
