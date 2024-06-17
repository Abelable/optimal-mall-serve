<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\Utils\AliOssServe;
use App\Utils\WxMpServe;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yansongda\LaravelPay\Facades\Pay;

class CommonController extends Controller
{
    protected $except = ['wxPayNotify'];

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

    public function wxPayNotify()
    {
        $data = Pay::wechat()->verify()->toArray();

        if (strpos($data['body'], 'order_sn_list')) {
            Log::info('order_wx_pay_notify', $data);
            DB::transaction(function () use ($data) {
                OrderService::getInstance()->wxPaySuccess($data);
            });
        }

        return Pay::wechat()->success();
    }
}
