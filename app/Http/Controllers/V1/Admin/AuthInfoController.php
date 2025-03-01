<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminTodoService;
use App\Services\AuthInfoService;
use App\Utils\CodeResponse;
use App\Utils\Enums\AdminTodoEnums;
use App\Utils\Inputs\AuthInfoPageInput;
use Illuminate\Support\Facades\DB;

class AuthInfoController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var AuthInfoPageInput $input */
        $input = AuthInfoPageInput::new();
        $columns = ['id', 'user_id', 'status', 'failure_reason', 'name', 'mobile', 'created_at', 'updated_at'];
        $page = AuthInfoService::getInstance()->getAuthInfoList($input, $columns);
        return $this->successPaginate($page);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $authInfo = AuthInfoService::getInstance()->getAuthInfoById($id);
        if (is_null($authInfo)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前实名认证信息不存在');
        }
        return $this->success($authInfo);
    }

    public function approved()
    {
        $id = $this->verifyRequiredId('id');

        $authInfo = AuthInfoService::getInstance()->getAuthInfoById($id);
        if (is_null($authInfo)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前实名认证信息不存在');
        }

        DB::transaction(function () use ($authInfo) {
            $authInfo->status = 1;
            $authInfo->save();

            AdminTodoService::getInstance()->deleteTodo(AdminTodoEnums::AUTH_CONFIRM, $authInfo->id);
        });


        return $this->success();
    }

    public function reject()
    {
        $id = $this->verifyRequiredId('id');
        $reason = $this->verifyRequiredString('failureReason');

        $authInfo = AuthInfoService::getInstance()->getAuthInfoById($id);
        if (is_null($authInfo)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前实名认证信息不存在');
        }

        DB::transaction(function () use ($authInfo, $reason) {
            $authInfo->status = 2;
            $authInfo->failure_reason = $reason;
            $authInfo->save();

            AdminTodoService::getInstance()->deleteTodo(AdminTodoEnums::AUTH_CONFIRM, $authInfo->id);
        });

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $authInfo = AuthInfoService::getInstance()->getAuthInfoById($id);
        if (is_null($authInfo)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前实名认证信息不存在');
        }
        $authInfo->delete();
        return $this->success();
    }

    public function getPendingCount()
    {
        $count = AuthInfoService::getInstance()->getCountByStatus(0);
        return $this->success($count);
    }
}
