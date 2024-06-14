<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\TeamLeaderService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\TeamLeaderPageInput;

class TeamLeaderController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var TeamLeaderPageInput $input */
        $input = TeamLeaderPageInput::new();
        $list = TeamLeaderService::getInstance()->getTeamLeaderList($input, ['id', 'status', 'name', 'mobile', 'created_at', 'updated_at']);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $teamLeader = TeamLeaderService::getInstance()->getTeamLeaderById($id);
        if (is_null($teamLeader)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前团长不存在');
        }
        $teamLeader->qualification_photo = json_decode($teamLeader->qualification_photo);
        return $this->success($teamLeader);
    }

    public function approved()
    {
        $id = $this->verifyRequiredId('id');
        $teamLeader = TeamLeaderService::getInstance()->getTeamLeaderById($id);
        if (is_null($teamLeader)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前团长不存在');
        }
        $teamLeader->status = 1;
        $teamLeader->save();

        // todo：短信通知用户

        return $this->success();
    }

    public function reject()
    {
        $id = $this->verifyRequiredId('id');
        $reason = $this->verifyRequiredString('failureReason');

        $teamLeader = TeamLeaderService::getInstance()->getTeamLeaderById($id);
        if (is_null($teamLeader)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前团长不存在');
        }

        $teamLeader->status = 2;
        $teamLeader->failure_reason = $reason;
        $teamLeader->save();

        // todo：短信通知用户

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $teamLeader = TeamLeaderService::getInstance()->getTeamLeaderById($id);
        if (is_null($teamLeader)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前团长不存在');
        }
        $teamLeader->delete();
        return $this->success();
    }
}
