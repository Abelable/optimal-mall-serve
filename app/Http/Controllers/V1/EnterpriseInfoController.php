<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\EnterpriseInfo;
use App\Services\EnterpriseInfoService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\EnterpriseInfoInput;

class EnterpriseInfoController extends Controller
{
    public function detail()
    {
        $enterpriseInfo = EnterpriseInfoService::getInstance()->getEnterpriseInfoByUserId($this->userId());
        return $this->success($enterpriseInfo);
    }

    public function add()
    {
        /** @var EnterpriseInfoInput $input */
        $input = EnterpriseInfoInput::new();
        EnterpriseInfoService::getInstance()->createEnterpriseInfo($this->userId(), $input);
        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        /** @var EnterpriseInfoInput $input */
        $input = EnterpriseInfoInput::new();

        /** @var EnterpriseInfo $enterpriseInfo */
        $enterpriseInfo = EnterpriseInfoService::getInstance()->getEnterpriseInfoById($id);
        if (is_null($enterpriseInfo)) {
            return $this->fail(CodeResponse::NOT_FOUND, '企业认证信息不存在');
        }
        EnterpriseInfoService::getInstance()->updateEnterpriseInfo($enterpriseInfo, $input);

        return $this->success();
    }

    public function delete()
    {
        $enterpriseInfo = EnterpriseInfoService::getInstance()->getEnterpriseInfoByUserId($this->userId());
        if (is_null($enterpriseInfo)) {
            return $this->fail(CodeResponse::NOT_FOUND, '企业认证信息不存在');
        }
        $enterpriseInfo->delete();
        return $this->success();
    }
}
