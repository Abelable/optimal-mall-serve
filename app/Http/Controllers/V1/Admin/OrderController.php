<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\OrderGoodsService;
use App\Services\OrderService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\OrderPageInput;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var OrderPageInput $input */
        $input = OrderPageInput::new();
        $columns = ['id', 'order_sn', 'status', 'payment_amount', 'consignee', 'mobile', 'created_at'];
        $list = OrderService::getInstance()->getOrderList($input, $columns);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $order = OrderService::getInstance()->getOrderById($id);
        if (is_null($order)) {
            return $this->fail(CodeResponse::NOT_FOUND, '订单不存在');
        }
        $goodsList = OrderGoodsService::getInstance()->getListByOrderId($order->id);
        $order['goods_list'] = $goodsList;
        return $this->success($order);
    }

    public function cancel()
    {
        $ids = $this->verifyArrayNotEmpty('ids', []);
        OrderService::getInstance()->systemCancel($this->userId(), $ids);
        return $this->success();
    }

    public function confirm()
    {
        $id = $this->verifyRequiredId('id');
        OrderService::getInstance()->confirm($this->userId(), $id);
        return $this->success();
    }

    public function delete()
    {
        $ids = $this->verifyArrayNotEmpty('ids', []);
        DB::transaction(function () use ($ids) {
            OrderService::getInstance()->delete($this->userId(), $ids);
        });
        return $this->success();
    }

    public function refund()
    {
        $id = $this->verifyRequiredId('id');
        OrderService::getInstance()->refund($this->userId(), $id);
        return $this->success();
    }
}
