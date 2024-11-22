<?php

namespace App\Utils;

use App\Utils\Traits\HttpClient;

class AddressAnalyzeServe
{
    use HttpClient;

    const URL = 'https://addre.market.alicloudapi.com/format?text=';

    public static function new()
    {
        return new static();
    }

    public function analyze($text)
    {
        return $this->httpGet(self::URL . $text, true, ['Authorization:APPCODE' => env('ADDRESS_ANALYZE_CODE')]);
    }
}
