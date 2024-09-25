<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\OrderGoodsService;
use App\Services\OrderService;
use App\Utils\CodeResponse;
use App\Utils\ExpressServe;
use App\Utils\Inputs\Admin\OrderPageInput;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var OrderPageInput $input */
        $input = OrderPageInput::new();
        $columns = ['id', 'order_sn', 'status', 'payment_amount', 'consignee', 'mobile', 'address', 'created_at'];
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

    public function delivery()
    {
        $id = $this->verifyRequiredInteger('id');
        $shipChannel = $this->verifyRequiredString('shipChannel');
        $shipCode = $this->verifyRequiredString('shipCode');
        $shipSn = $this->verifyRequiredString('shipSn');

        OrderService::getInstance()->ship($id, $shipChannel, $shipCode, $shipSn);

        // todo: 管理员操组记录

        return $this->success();
    }

    public function cancel()
    {
        $ids = $this->verifyArrayNotEmpty('ids');
        OrderService::getInstance()->adminCancel($ids);

        // todo: 管理员操组记录

        return $this->success();
    }

    public function shippingInfo()
    {
        $id = $this->verifyRequiredId('id');
        $order = OrderService::getInstance()->getOrderById($id);
        if (is_null($order)) {
            return $this->fail(CodeResponse::NOT_FOUND, '订单不存在');
        }
        $result = ExpressServe::new()->track($order->ship_code, $order->ship_sn, $order->mobile);
        return $this->success([
            'shipChannel' => $order->ship_channel,
            'shipSn' => $order->ship_sn,
            'traces' => $result['Traces']
        ]);
    }

    public function confirm()
    {
        $ids = $this->verifyArrayNotEmpty('ids');
        OrderService::getInstance()->adminConfirm($ids);
        return $this->success();
    }

    public function delete()
    {
        $ids = $this->verifyArrayNotEmpty('ids', []);
        $orderList = OrderService::getInstance()->getOrderListByIds($ids);
        if (count($orderList) == 0) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL, '订单不存在');
        }
        DB::transaction(function () use ($orderList) {
            OrderService::getInstance()->delete($orderList);
        });
        return $this->success();
    }
}
