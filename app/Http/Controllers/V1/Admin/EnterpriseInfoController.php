<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminTodoService;
use App\Services\EnterpriseInfoService;
use App\Utils\CodeResponse;
use App\Utils\Enums\AdminTodoEnums;
use App\Utils\Inputs\EnterpriseInfoPageInput;
use Illuminate\Support\Facades\DB;

class EnterpriseInfoController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var EnterpriseInfoPageInput $input */
        $input = EnterpriseInfoPageInput::new();
        $columns = ['id', 'user_id', 'status', 'failure_reason', 'name', 'created_at', 'updated_at'];
        $page = EnterpriseInfoService::getInstance()->getEnterpriseInfoList($input, $columns);
        return $this->successPaginate($page);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $authInfo = EnterpriseInfoService::getInstance()->getEnterpriseInfoById($id);
        if (is_null($authInfo)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前企业认证信息不存在');
        }
        return $this->success($authInfo);
    }

    public function approved()
    {
        $id = $this->verifyRequiredId('id');

        $authInfo = EnterpriseInfoService::getInstance()->getEnterpriseInfoById($id);
        if (is_null($authInfo)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前企业认证信息不存在');
        }

        DB::transaction(function () use ($authInfo) {
            $authInfo->status = 1;
            $authInfo->save();

            AdminTodoService::getInstance()->deleteTodo(AdminTodoEnums::ENTERPRISE_CONFIRM, $authInfo->id);
        });


        return $this->success();
    }

    public function reject()
    {
        $id = $this->verifyRequiredId('id');
        $reason = $this->verifyRequiredString('failureReason');

        $authInfo = EnterpriseInfoService::getInstance()->getEnterpriseInfoById($id);
        if (is_null($authInfo)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前企业认证信息不存在');
        }

        DB::transaction(function () use ($authInfo, $reason) {
            $authInfo->status = 2;
            $authInfo->failure_reason = $reason;
            $authInfo->save();

            AdminTodoService::getInstance()->deleteTodo(AdminTodoEnums::ENTERPRISE_CONFIRM, $authInfo->id);
        });

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $authInfo = EnterpriseInfoService::getInstance()->getEnterpriseInfoById($id);
        if (is_null($authInfo)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前企业认证信息不存在');
        }
        $authInfo->delete();
        return $this->success();
    }

    public function getPendingCount()
    {
        $count = EnterpriseInfoService::getInstance()->getCountByStatus(0);
        return $this->success($count);
    }
}
