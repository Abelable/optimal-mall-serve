<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\TeamLeaderService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\TeamLeaderInput;

class TeamLeaderController extends Controller
{
    public function addTeamLeader()
    {
        /** @var TeamLeaderInput $input */
        $input = TeamLeaderInput::new();

        $teamLeader = TeamLeaderService::getInstance()->getTeamLeaderByUserId($this->userId());
        if (!is_null($teamLeader)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '您已提交团长申请');
        }

        TeamLeaderService::getInstance()->createMerchant($input, $this->userId());
        return $this->success();
    }

    public function statusInfo()
    {
        $statusInfo = TeamLeaderService::getInstance()->getTeamLeaderByUserId($this->userId(), [0, 1, 2], ['id', 'status', 'failure_reason']);
        return $this->success($statusInfo ?: null);
    }

    public function reapply()
    {
        $teamLeader = TeamLeaderService::getInstance()->getTeamLeaderByUserId($this->userId());
        if (is_null($teamLeader)) {
            return $this->fail(CodeResponse::NOT_FOUND, '您暂未提交团长申请，无法操作');
        }

        $teamLeader->status = 0;
        $teamLeader->save();

        return $this->success($teamLeader);
    }

    public function userInfo()
    {
        $userId = $this->verifyRequiredId('userId');
        $teamLeader = TeamLeaderService::getInstance()->getTeamLeaderByUserId($userId, [1]);
        if (is_null($teamLeader)) {
            return $this->fail(CodeResponse::NOT_FOUND, '团长身份无效');
        }

        $columns = ['id', 'avatar', 'nickname', 'signature'];
        $user = UserService::getInstance()->getUserById($userId, $columns);

        return $this->success($user);
    }
}
