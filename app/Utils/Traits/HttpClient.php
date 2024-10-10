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

    public function httpPost($url, $data, $dataType = 1, $needDecode = true)
    {
        $client = new Client();

        switch ($dataType) {
            case 1:
                $response = $client->request('POST', $url, ['json' => $data]);
                break;

            case 2:
                $response = $client->request('POST', $url, ['form_params' => $data]);
                break;

            case 3:
                $jsonData = json_encode($data, JSON_UNESCAPED_UNICODE);
                $response = $client->request('POST', $url, ['body' => $jsonData, 'headers' => ['Content-Type' => 'application/json']]);
                break;
        }

        return $needDecode ? json_decode((string) $response->getBody(), true) : $response->getBody();
    }
}
