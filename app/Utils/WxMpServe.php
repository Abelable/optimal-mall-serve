<?php

namespace App\Utils;

use App\Models\Activity;
use App\Utils\Traits\HttpClient;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class WxMpServe
{
    use HttpClient;

    const ACCESS_TOKEN_KEY = 'wx_mp_access_token';
    const STABLE_ACCESS_TOKEN_KEY = 'wx_mp_stable_access_token';
    const GET_ACCESS_TOKEN_URL = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';
    const GET_STABLE_ACCESS_TOKEN_URL = 'https://api.weixin.qq.com/cgi-bin/stable_token';
    const GET_PHONE_NUMBER_URL = 'https://api.weixin.qq.com/wxa/business/getuserphonenumber?access_token=%s';
    const GET_OPENID_URL = 'https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code';
    const GET_QRCODE_URL = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=%s';
    const SEND_MSG_URL = 'https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token=%s';

    protected $accessToken;
    protected $stableAccessToken;

    public static function new()
    {
        return new static();
    }

    public function __construct()
    {
        $this->accessToken = Cache::has(self::ACCESS_TOKEN_KEY) ? Cache::get(self::ACCESS_TOKEN_KEY) : $this->getAccessToken();
        $this->stableAccessToken = Cache::has(self::STABLE_ACCESS_TOKEN_KEY) ? Cache::get(self::STABLE_ACCESS_TOKEN_KEY) : $this->getStableAccessToken();
    }

    private function getAccessToken()
    {
        $result = $this->httpGet(sprintf(self::GET_ACCESS_TOKEN_URL, env('WX_MP_APPID'), env('WX_MP_SECRET')));
        if (!empty($result['errcode'])) {
            throw new \Exception('获取微信小程序access_token异常：' . $result['errcode'] . $result['errmsg']);
        }
        $accessToken = $result['access_token'];
        Cache::put(self::ACCESS_TOKEN_KEY, $accessToken, now()->addSeconds($result['expires_in'] - 300));
        return $accessToken;
    }

    private function getStableAccessToken()
    {
        $result = $this->httpPost(self::GET_STABLE_ACCESS_TOKEN_URL, ['grant_type' => 'client_credential', 'appid' => env('WX_MP_APPID'), 'secret' => env('WX_MP_SECRET')]);
        if (!empty($result['errcode'])) {
            throw new \Exception('获取微信小程序stable_access_token异常：' . $result['errcode'] . $result['errmsg']);
        }
        $stableAccessToken = $result['access_token'];
        Cache::put(self::STABLE_ACCESS_TOKEN_KEY, $stableAccessToken, now()->addSeconds($result['expires_in'] - 300));
        return $stableAccessToken;
    }

    public function getUserPhoneNumber($code)
    {
        $result = $this->httpPost(sprintf(self::GET_PHONE_NUMBER_URL, $this->accessToken), ['code' => $code]);
        if ($result['errcode'] != 0) {
            throw new \Exception('获取微信小程序用户手机号异常：' . $result['errcode'] . $result['errmsg']);
        }
        return $result['phone_info']['purePhoneNumber'];
    }

    public function getUserOpenid($code)
    {
        $result = $this->httpGet(sprintf(self::GET_OPENID_URL, env('WX_MP_APPID'), env('WX_MP_SECRET'), $code));
        if (!empty($result['errcode'])) {
            throw new \Exception('获取微信小程序openid异常：' . $result['errcode'] . $result['errmsg']);
        }
        return $result;
    }

    public function getQRCode($scene, $page)
    {
        return $this->httpPost(sprintf(self::GET_QRCODE_URL, $this->accessToken), ['scene' => $scene, 'page' => $page], false, false);
    }

    public function sendActivityStartMsg($openid, Activity $activity)
    {
        $endTime = Carbon::parse($activity->end_time)->format('Y-m-d H:i:s');
        $data = [
            'thing7' => ['value' => $activity->name],
            'thing8' => ['value' => $activity->goods_name],
            'date5' => ['value' => $endTime]
        ];
        $result = $this->httpPost(
            sprintf(self::SEND_MSG_URL, $this->stableAccessToken),
            [
                'template_id' => env('ACTIVITY_TEMPLATE_ID'),
                'page' => env('ACTIVITY_PAGE') . $activity->goods_id,
                'touser' => $openid,
                'data' => $data
            ]
        );
        dd($result);
        return $result;
    }
}
