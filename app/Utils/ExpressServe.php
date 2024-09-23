<?php

namespace App\Utils;

use App\Utils\Traits\HttpClient;

class ExpressServe
{
    use HttpClient;

    const URL = 'https://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx';

    public static function new()
    {
        return new static();
    }

    public function track($shipperCode, $logisticCode, $mobile)
    {
        if ($shipperCode == 'SF') {
            $CustomerName = substr($mobile,-4);
            $requestData = "{'OrderCode':'', 'ShipperCode':'{$shipperCode}', 'LogisticCode':'{$logisticCode}', 'CustomerName':{$CustomerName}}}";
        } else {
            $requestData = "{'OrderCode':'', 'ShipperCode':'{$shipperCode}', 'LogisticCode':'{$logisticCode}'}";
        }
        $result = $this->httpPost(self::URL, $this->formatReqData($requestData, '1002'), true);
        return $this->formatResData($result);
    }

    protected function formatReqData($requestData, $RequestType)
    {
        $datas = array(
            'EBusinessID' => env('KDNIAO_EBUSINESS_ID'),
            'RequestType' => $RequestType,
            'RequestData' => urlencode($requestData),
            'DataType' => 2,
        );
        $datas['DataSign'] = $this->encrypt($requestData);
        return $datas;
    }

    protected function formatResData($result)
    {
        $result = json_decode($result, true);
        if ($result['Success'] == false) {
            return $result['ResponseData'];
        }
        $result2 = json_decode($result['ResponseData'], true);

        if ($result2['Success'] == false) {
            return $result2['Reason'];
        }
        return $result2;
    }

    protected function encrypt($data)
    {
        return urlencode(base64_encode(md5($data . env('KDNIAO_API_KEY'))));
    }
}
