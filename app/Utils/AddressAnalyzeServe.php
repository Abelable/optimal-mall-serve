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
        $result = $this->httpGet(self::URL . $text, true, ['Authorization' => 'APPCODE ' . env('ADDRESS_ANALYZE_CODE')]);
        if ($result['code'] != 1) {
            throw new \Exception('解析地址「' . $result['text'] . '」异常：' . $result['msg']);
        }
        return $result['data'];
    }
}
