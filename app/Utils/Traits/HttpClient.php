<?php

namespace App\Utils\Traits;

use GuzzleHttp\Client;

trait HttpClient
{
    public function httpGet($url, $needDecode = true)
    {
        $client = new Client();
        $response = $client->request('GET', $url);
        return $needDecode ? json_decode((string) $response->getBody(), true) : $response->getBody();
    }

    public function httpPost($url, $data, $form = false, $needDecode = true)
    {
        $client = new Client();
        if ($form) {
            $response = $client->request('POST', $url, ['form_params' => $data]);
        } else {
            $response = $client->request('POST', $url, ['json' => $data]);
        }
        return $needDecode ? json_decode((string) $response->getBody(), true) : $response->getBody();
    }
}
