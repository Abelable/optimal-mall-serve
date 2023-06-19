<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\ScenicProviderService;
use App\Services\ScenicTicketService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\ScenicTicketListInput;

class ScenicTicketController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var ScenicTicketListInput $input */
        $input = ScenicTicketListInput::new();
        $list = ScenicTicketService::getInstance()->getList($input);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $ticket = ScenicTicketService::getInstance()->getTicketById($id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前门票不存在');
        }

        $provider = ScenicProviderService::getInstance()->getProviderById($ticket->provider_id);
        if (is_null($provider)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前服务商不存在');
        }

        $ticket['provider_info'] = $provider;

        return $this->success($ticket);
    }

    public function approve()
    {
        $id = $this->verifyRequiredId('id');

        $ticket = ScenicTicketService::getInstance()->getTicketById($id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前门票不存在');
        }
        $ticket->status = 1;
        $ticket->save();

        return $this->success();
    }

    public function reject()
    {
        $id = $this->verifyRequiredId('id');
        $reason = $this->verifyRequiredString('failureReason');

        $ticket = ScenicTicketService::getInstance()->getTicketById($id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前门票不存在');
        }
        $ticket->status = 2;
        $ticket->failure_reason = $reason;
        $ticket->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        $ticket = ScenicTicketService::getInstance()->getTicketById($id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前门票不存在');
        }
        $ticket->delete();

        return $this->success();
    }
}
