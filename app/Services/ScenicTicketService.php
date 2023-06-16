<?php

namespace App\Services;

use App\Models\ScenicTicket;
use App\Utils\Inputs\ScenicTicketInput;
use App\Utils\Inputs\StatusPageInput;

class ScenicTicketService extends BaseService
{
    public function getListTotal($userId, $status)
    {
        return ScenicTicket::query()->where('user_id', $userId)->where('status', $status)->count();
    }

    public function getTicketListByStatus($userId, StatusPageInput $input, $columns=['*'])
    {
        return ScenicTicket::query()
            ->where('user_id', $userId)
            ->where('status', $input->status)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getTicketById($id, $columns=['*'])
    {
        return ScenicTicket::query()->find($id, $columns);
    }

    public function getUserTicket($userId, $id, $columns=['*'])
    {
        return ScenicTicket::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function createTicket($userId, $providerId, $shopId, ScenicTicketInput $input)
    {
        $ticket = ScenicTicket::new();
        $ticket->user_id = $userId;
        $ticket->provider_id = $providerId;
        $ticket->shop_id = $shopId;

        return $this->updateTicket($ticket, $input);
    }

    public function updateTicket(ScenicTicket $ticket, ScenicTicketInput $input)
    {
        $ticket->type = $input->type;
        $ticket->name = $input->name;
        $ticket->price = $input->price;
        $ticket->market_price = $input->marketPrice ?: '';
        $ticket->sales_commission_rate = $input->salesCommissionRate;
        $ticket->promotion_commission_rate = $input->promotionCommissionRate;
        $ticket->fee_include_tips = $input->feeIncludeTips ?: '';
        $ticket->fee_not_include_tips = $input->feeNotIncludeTips ?: '';
        $ticket->booking_time = $input->bookingTime;
        $ticket->effective_time = $input->effectiveTime ?: 0;
        $ticket->validity_time = $input->validityTime ?: 0;
        $ticket->limit_number = $input->limitNumber ?: 0;
        $ticket->refund_status = $input->refundStatus;
        $ticket->refund_tips = $input->refundTips ?: '';
        $ticket->need_exchange = $input->needExchange;
        $ticket->exchange_tips = $input->exchangeTips ?: '';
        $ticket->exchange_time = $input->exchangeTime ?: '';
        $ticket->exchange_location = $input->exchangeLocation ?: '';
        $ticket->enter_time = $input->enterTime ?: '';
        $ticket->enter_location = $input->enterLocation ?: '';
        $ticket->invoice_tips = $input->invoiceTips ?: '';
        $ticket->reminder_tips = $input->reminderTips ?: '';
        $ticket->save();

        return $ticket;
    }
}
