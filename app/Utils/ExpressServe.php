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
        $result = $this->httpPost(self::URL, $this->formatReqData($requestData, '1002'), 2);
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
        if ($result['Success'] == false) {
            throw new \Exception('物流信息获取异常：' . $result['Reason']);
        }
        return array_reverse($result['Traces']);
    }

    protected function encrypt($data)
    {
        return urlencode(base64_encode(md5($data . env('KDNIAO_API_KEY'))));
    }
}
