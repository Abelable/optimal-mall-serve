<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Goods;
use App\Models\Merchant;
use App\Services\GoodsRefundAddressService;
use App\Services\GoodsService;
use App\Services\MerchantManagerService;
use App\Services\MerchantRefundAddressService;
use App\Services\MerchantService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\MerchantInput;
use App\Utils\Inputs\Admin\MerchantListInput;
use Illuminate\Support\Facades\DB;

class MerchantController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var MerchantListInput $input */
        $input = MerchantListInput::new();
        $page = MerchantService::getInstance()->getMerchantPage($input);
        $list = collect($page->items())->map(function (Merchant $merchant) {
            $merchant->license = json_decode($merchant->license);
            return $merchant;
        });
        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $merchant = MerchantService::getInstance()->getMerchantById($id);
        if (is_null($merchant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商家不存在');
        }
        $merchant->license = json_decode($merchant->license);
        $merchant['managerIds'] = $merchant->managerIds();
        return $this->success($merchant);
    }

    public function add()
    {
        /** @var MerchantInput $input */
        $input = MerchantInput::new();
        $merchant = Merchant::new();

        DB::transaction(function () use ($input, $merchant) {
            $merchant = MerchantService::getInstance()->updateMerchant($merchant, $input);
            foreach ($input->managerIds as $managerId) {
                MerchantManagerService::getInstance()->createManager($merchant->id, $managerId);
            }
        });

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        /** @var MerchantInput $input */
        $input = MerchantInput::new();

        $merchant = MerchantService::getInstance()->getMerchantById($id);
        if (is_null($merchant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商家不存在');
        }

        DB::transaction(function () use ($input, $merchant) {
            $merchant = MerchantService::getInstance()->updateMerchant($merchant, $input);

            $managerIds = $merchant->managerIds()->toArray();
            $existingManagerIds = implode(',', $managerIds);
            $inputManagerIds = implode(',', $input->managerIds);
            if ($existingManagerIds != $inputManagerIds) {
                MerchantManagerService::getInstance()->deleteManager($merchant->id);
                foreach ($input->managerIds as $managerId) {
                    MerchantManagerService::getInstance()->createManager($merchant->id, $managerId);
                }
            }
        });
        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $merchant = MerchantService::getInstance()->getMerchantById($id);
        if (is_null($merchant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商家不存在');
        }
        $merchant->delete();
        return $this->success();
    }

    public function options()
    {
        $options = MerchantService::getInstance()->getMerchantOptions(['id', 'name']);
        return $this->success($options);
    }

    public function initRefundAddress()
    {
        $list = MerchantService::getInstance()->getMerchantOptions();
        $list->map(function (Merchant $merchant) {
            $refundAddress = MerchantRefundAddressService::getInstance()
                ->createAddress($merchant->id, $merchant->consignee_name, $merchant->mobile, $merchant->address_detail);
            $goodsList = GoodsService::getInstance()->getGoodsListByMerchantId($merchant->id);
            $goodsList->map(function (Goods $goods) use ($refundAddress) {
                GoodsRefundAddressService::getInstance()->createAddress($goods->id, $refundAddress->id);
            });
        });
        return $this->success();
    }
}
