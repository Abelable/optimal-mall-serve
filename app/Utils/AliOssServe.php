<?php

namespace App\Utils;

class AliOssServe
{
    public static function new()
    {
        return new static();
    }

    public function getOssConfig()
    {
        // 失效时间
        $expire = 600;
        $end = time() + $expire;
        $expiration = $this->gmtIso8601($end);

        // 文件大小限制
        $conditions[] = ['content-length-range', 0, 1048576000];
        // 设置上传目录
        $dir = 'img/' . date('Ymd') . '/';
        $conditions[] = ['starts-with', '$key', $dir];

        $policy = base64_encode(json_encode([
            'expiration' => $expiration,
            'conditions' => $conditions
        ]));

        $signature = base64_encode(hash_hmac('sha1', $policy, env('ALI_OSS_ACCESS_KEY_SECRET'), true));

        $callbackUrl = '';
        $callback = base64_encode(json_encode([
            'callbackUrl' => $callbackUrl,
            'callbackBody' => '',
            'callbackBodyType' => 'application/json;charset=UTF-8'
        ]));

        return [
            'accessId' => env('ALI_OSS_ACCESS_KEY_ID'),
            'host' => env('ALI_OSS_HOST'),
            'policy' => $policy,
            'signature' => $signature,
            'expire' => $end,
            'callback' => $callback,
            'dir' => $dir
        ];
    }

    private function gmtIso8601($time)
    {
        return str_replace('+00:00', '.000Z', gmdate('c', $time));
    }
}
