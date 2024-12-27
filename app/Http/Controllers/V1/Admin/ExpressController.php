<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Express;
use App\Services\ExpressService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\ExpressPageInput;

class ExpressController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var ExpressPageInput $input */
        $input = ExpressPageInput::new();
        $list = ExpressService::getInstance()->getExpressList($input);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $express = ExpressService::getInstance()->getExpressById($id);
        if (is_null($express)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前快递不存在');
        }
        return $this->success($express);
    }

    public function add()
    {
        $code = $this->verifyRequiredString('code');
        $name = $this->verifyRequiredString('name');

        $express = ExpressService::getInstance()->getExpressByCode($code);
        if (!is_null($express)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '当前快递已存在');
        }

        $express = Express::new();
        $express->code = $code;
        $express->name = $name;
        $express->save();

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        $code = $this->verifyRequiredString('code');
        $name = $this->verifyRequiredString('name');

        $express = ExpressService::getInstance()->getExpressByCode($code);
        if (!is_null($express)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '当前快递已存在');
        }

        $express = ExpressService::getInstance()->getExpressById($id);
        if (is_null($express)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前快递不存在');
        }

        $express->code = $code;
        $express->name = $name;
        $express->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $express = ExpressService::getInstance()->getExpressById($id);
        if (is_null($express)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前快递不存在');
        }
        $express->delete();
        return $this->success();
    }

    public function options()
    {
        $options = ExpressService::getInstance()->getExpressOptions(['id', 'code', 'name']);
        return $this->success($options);
    }
}
