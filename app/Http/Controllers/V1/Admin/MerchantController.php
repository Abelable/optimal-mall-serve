<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Services\Admin\MerchantService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\MerchantInput;
use App\Utils\Inputs\Admin\MerchantListInput;

class MerchantController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var MerchantListInput $input */
        $input = MerchantListInput::new();
        $list = MerchantService::getInstance()->getMerchantList($input);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $merchant = MerchantService::getInstance()->getMerchantById($id);
        if (is_null($merchant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商家不存在');
        }
        return $this->success($merchant);
    }

    public function add()
    {
        /** @var MerchantInput $input */
        $input = MerchantInput::new();
        $merchant = Merchant::new();
        MerchantService::getInstance()->updateMerchant($merchant, $input);
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

        MerchantService::getInstance()->updateMerchant($merchant, $input);
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
}
