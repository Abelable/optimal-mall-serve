<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\Utils\AliOssServe;
use App\Utils\ExpressServe;
use App\Utils\WxMpServe;
use Illuminate\Support\Facades\Log;
use Yansongda\LaravelPay\Facades\Pay;
use Yansongda\Pay\Exceptions\GatewayException;

class CommonController extends Controller
{
    protected $except = ['ossConfig', 'wxPayNotify'];

    public function ossConfig()
    {
        $config = AliOssServe::new()->getOssConfig();
        return $this->success($config);
    }

    public function wxQRCode()
    {
        $scene = $this->verifyRequiredString('scene');
        $page = $this->verifyRequiredString('page');

        $imageData = WxMpServe::new()->getQRCode($scene, $page);
        $qrcode = 'data:image/png;base64,' . base64_encode($imageData);

        return $this->success($qrcode);

//        return response($imageData)
//            ->header('Content-Type', 'image/png')
//            ->header('Content-Disposition', 'inline');
    }

    private function fileToBase64($file){
        $base64_file = '';
        if(file_exists($file)){
            $mime_type= mime_content_type($file);
            $base64_data = base64_encode(file_get_contents($file));
            $base64_file = 'data:'.$mime_type.';base64,'.$base64_data;
        }
        return $base64_file;
    }

    /**
     * @throws \Yansongda\Pay\Exceptions\InvalidArgumentException
     * @throws \Yansongda\Pay\Exceptions\InvalidSignException
     */
    public function wxPayNotify()
    {
        try {
            $data = Pay::wechat()->verify()->toArray();
            Log::info('order_wx_pay_notify', $data);
            OrderService::getInstance()->wxPaySuccess($data);
        } catch (GatewayException $exception) {
            Log::error('wx_pay_notify_fail', [$exception]);
        }
        return Pay::wechat()->success();
    }

    public function shippingInfo()
    {
        $shipCode = $this->verifyRequiredString('shipCode');
        $shipSn = $this->verifyRequiredString('shipSn');
        $mobile = $this->verifyString('mobile');

        $traces = ExpressServe::new()->track($shipCode, $shipSn, $mobile);
        return $this->success($traces);
    }
}
