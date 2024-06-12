<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\TeamLeaderService;
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
        $statusInfo = TeamLeaderService::getInstance()->getTeamLeaderByUserId($this->userId(), ['id', 'status', 'failure_reason']);
        return $this->success($statusInfo ?: null);
    }

    public function reapply()
    {
        $teamLeader = TeamLeaderService::getInstance()->getTeamLeaderByUserId($this->userId());
        if (is_null($teamLeader)) {
            return $this->fail(CodeResponse::NOT_FOUND, '您暂未提交团长申请，无法删除');
        }

        $teamLeader->status = 0;
        $teamLeader->save();

        return $this->success($teamLeader);
    }
}
