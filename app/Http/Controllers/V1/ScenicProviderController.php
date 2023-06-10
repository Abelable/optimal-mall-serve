<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ProviderScenicSpot;
use App\Models\ScenicSpot;
use App\Services\ProviderScenicSpotService;
use App\Services\ScenicProviderOrderService;
use App\Services\ScenicProviderService;
use App\Services\ScenicService;
use App\Services\ScenicShopService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\ProviderScenicSpotListInput;
use App\Utils\Inputs\ScenicProviderInput;
use Illuminate\Support\Facades\DB;
use Yansongda\LaravelPay\Facades\Pay;

class ScenicProviderController extends Controller
{
    public function settleIn()
    {
        /** @var ScenicProviderInput $input */
        $input = ScenicProviderInput::new();

        $provider = ScenicProviderService::getInstance()->getProviderByUserId($this->userId());
        if (!is_null($provider)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '您已提交申请，请勿重复操作');
        }

        DB::transaction(function () use ($input) {
            $provider = ScenicProviderService::getInstance()->createProvider($input, $this->userId());
            ScenicShopService::getInstance()->createShop($this->userId(), $provider->id, $input);
        });

        return $this->success();
    }

    public function statusInfo()
    {
        $provider = ScenicProviderService::getInstance()->getProviderByUserId($this->userId(), ['id', 'status', 'failure_reason']);
        $providerOrder = ScenicProviderOrderService::getInstance()->getOrderByUserId($this->userId(), ['id']);

        return $this->success($provider ? [
            'id' => $provider->id,
            'status' => $provider->status,
            'failureReason' => $provider->failure_reason,
            'orderId' => $providerOrder->id
        ] : null);
    }

    public function payDeposit()
    {
        $orderId = $this->verifyRequiredId('orderId');
        $order = ScenicProviderOrderService::getInstance()->getWxPayOrder($this->userId(), $orderId, $this->user()->openid);
        $payParams = Pay::wechat()->miniapp($order);
        return $this->success($payParams);
    }

    public function deleteProvider()
    {
        $provider = ScenicProviderService::getInstance()->getProviderByUserId($this->userId());
        if (is_null($provider)) {
            return $this->fail(CodeResponse::NOT_FOUND, '景区服务商信息不存在');
        }
        $provider->delete();
        return $this->success();
    }

    public function myShopInfo()
    {
        $columns = ['id', 'name', 'type', 'avatar', 'cover'];
        $shop = ScenicShopService::getInstance()->getShopByUserId($this->userId(), $columns);
        if (is_null($shop)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前店铺不存在');
        }
        return $this->success($shop);
    }

    public function providerScenicSpotList()
    {
        /** @var ProviderScenicSpotListInput $input */
        $input = ProviderScenicSpotListInput::new();

        $page = ProviderScenicSpotService::getInstance()->getSpotList($this->userId(), $input, ['id', 'scenic_id', 'status', 'failure_reason', 'created_at', 'updated_at']);
        $providerScenicSpotList = collect($page->items());
        $scenicIds = $providerScenicSpotList->pluck('scenic_id')->toArray();
        $scenicList = ScenicService::getInstance()->getScenicListByIds($scenicIds, ['id', 'name', 'image_list', 'level', 'address'])->keyBy('id');
        $list = $providerScenicSpotList->map(function (ProviderScenicSpot $providerScenicSpot) use ($scenicList) {
            /** @var ScenicSpot $scenic */
            $scenic = $scenicList->get($providerScenicSpot->scenic_id);
            $providerScenicSpot['scenic_image'] = json_decode($scenic->image_list)[0];
            $providerScenicSpot['scenic_name'] = $scenic->name;
            $providerScenicSpot['scenic_level'] = $scenic->name;
            $providerScenicSpot['scenic_address'] = $scenic->name;
            return $providerScenicSpot;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function applyScenicSpot()
    {
        $scenicId = $this->verifyRequiredId('scenicId');
        $scenicSpot = ScenicService::getInstance()->getScenicById($scenicId);
        if (is_null($scenicSpot)) {
            return $this->fail(CodeResponse::NOT_FOUND, '景点不存在');
        }
        if (is_null($this->user()->scenicShop)) {
            return $this->fail(CodeResponse::NOT_FOUND, '暂无权限申请添加景点');
        }
        $providerScenicSpot = ProviderScenicSpot::new();
        $providerScenicSpot->user_id = $this->userId();
        $providerScenicSpot->provider_id = $this->user()->scenicProvider->id;
        $providerScenicSpot->scenic_id = $scenicId;
        $providerScenicSpot->save();
        return $this->success();
    }

    public function deleteProviderScenicSpot()
    {
        $id = $this->verifyRequiredId('id');
        $spot = ProviderScenicSpotService::getInstance()->getSpotById($this->userId(), $id);
        if (is_null($spot)) {
            return $this->fail(CodeResponse::NOT_FOUND, '供应商景点不存在');
        }
        $spot->delete();
        return $this->success();
    }
}
