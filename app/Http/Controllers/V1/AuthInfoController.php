<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\AuthInfo;
use App\Services\AdminTodoService;
use App\Services\AuthInfoService;
use App\Utils\CodeResponse;
use App\Utils\Enums\NotificationEnums;
use App\Utils\Inputs\AuthInfoInput;
use Illuminate\Support\Facades\DB;

class AuthInfoController extends Controller
{
    public function detail()
    {
        $authInfo = AuthInfoService::getInstance()->getAuthInfoByUserId($this->userId());
        return $this->success($authInfo);
    }

    public function add()
    {
        /** @var AuthInfoInput $input */
        $input = AuthInfoInput::new();

        DB::transaction(function () use ($input) {
            $authInfo = AuthInfoService::getInstance()->createAuthInfo($this->userId(), $input);
            AdminTodoService::getInstance()->createTodo(NotificationEnums::AUTH_NOTICE, [$authInfo->id]);
        });

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        /** @var AuthInfoInput $input */
        $input = AuthInfoInput::new();

        /** @var AuthInfo $authInfo */
        $authInfo = AuthInfoService::getInstance()->getAuthInfoById($id);
        if (is_null($authInfo)) {
            return $this->fail(CodeResponse::NOT_FOUND, '实名认证信息不存在');
        }
        AuthInfoService::getInstance()->updateAuthInfo($authInfo, $input);

        return $this->success();
    }

    public function delete()
    {
        $authInfo = AuthInfoService::getInstance()->getAuthInfoByUserId($this->userId());
        if (is_null($authInfo)) {
            return $this->fail(CodeResponse::NOT_FOUND, '实名认证信息不存在');
        }
        $authInfo->delete();
        return $this->success();
    }
}
