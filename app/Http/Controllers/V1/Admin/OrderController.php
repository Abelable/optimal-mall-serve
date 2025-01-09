<?php

namespace App\Http\Controllers\V1\Admin;

use App\Exceptions\BusinessException;
use App\Exports\OrdersExport;
use App\Http\Controllers\Controller;
use App\Imports\OrdersImport;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Services\GoodsService;
use App\Services\OrderGoodsService;
use App\Services\OrderService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\ExpressServe;
use App\Utils\Inputs\Admin\OrderPageInput;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    protected $guard = 'Admin';

    public function shipOrderCount()
    {
        $count = OrderService::getInstance()->getOrderCountByStatusList([201, 204]);
        return $this->success($count);
    }

    public function orderedGoodsOptions()
    {
        $goodsIds = array_unique(OrderGoodsService::getInstance()->getList()->pluck('goods_id')->toArray());
        $goodsOptions = GoodsService::getInstance()->getListByIds($goodsIds, ['id', 'cover', 'name']);
        return $this->success($goodsOptions);
    }

    public function orderedUserOptions()
    {
        $userIds = OrderService::getInstance()->getOrderList()->pluck('user_id')->toArray();
        $userOptions = UserService::getInstance()->getListByIds($userIds, ['id', 'avatar', 'nickname']);
        return $this->success($userOptions);
    }
    public function list()
    {
        /** @var OrderPageInput $input */
        $input = OrderPageInput::new();
        $columns = ['id', 'user_id', 'order_sn', 'status', 'merchant_id', 'refund_amount', 'consignee', 'mobile', 'address', 'created_at', 'updated_at'];
        $page = OrderService::getInstance()->getOrderPage($input, $columns);
        $orderList = collect($page->items());

        $userIds = $orderList->pluck('user_id')->toArray();
        $userList = UserService::getInstance()->getListByIds($userIds, ['id', 'avatar', 'nickname'])->keyBy('id');

        $orderIds = $orderList->pluck('id')->toArray();
        $goodsListColumns = ['order_id', 'goods_id', 'cover', 'name', 'selected_sku_name', 'price', 'number'];
        $groupedGoodsList = OrderGoodsService::getInstance()->getListByOrderIds($orderIds, $goodsListColumns)->groupBy('order_id');

        $list = $orderList->map(function (Order $order) use ($userList, $groupedGoodsList) {
            $user = $userList->get($order->user_id);
            $order['userInfo'] = $user;
            unset($order->user_id);

            $goodsList = $groupedGoodsList->get($order->id)->map(function (OrderGoods $orderGoods) use ($order) {
                return [
                    'id' => $orderGoods->goods_id,
                    'cover' => $orderGoods->cover,
                    'name' => $orderGoods->name,
                    'skuName' => $orderGoods->selected_sku_name,
                    'price' => $orderGoods->price,
                    'number' => $orderGoods->number,
                ];
            });
            $order['goodsList'] = $goodsList;
            return $order;
        });

        return $this->success($this->paginate($page, $list));
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

    public function shippingInfo()
    {
        $id = $this->verifyRequiredId('id');
        $order = OrderService::getInstance()->getOrderById($id);
        if (is_null($order)) {
            return $this->fail(CodeResponse::NOT_FOUND, '订单不存在');
        }
        $traces = ExpressServe::new()->track($order->ship_code, $order->ship_sn, $order->mobile);
        return $this->success([
            'shipChannel' => $order->ship_channel,
            'shipSn' => $order->ship_sn,
            'traces' => $traces
        ]);
    }

    public function cancel()
    {
        $ids = $this->verifyArrayNotEmpty('ids');
        OrderService::getInstance()->adminCancel($ids);

        // todo: 管理员操组记录

        return $this->success();
    }

    public function refund()
    {
        $ids = $this->verifyArrayNotEmpty('ids');
        OrderService::getInstance()->adminRefund($ids);

        // todo: 管理员操组记录

        return $this->success();
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

    public function export()
    {
        $ids = $this->verifyArrayNotEmpty('ids', []);

        OrderService::getInstance()->exportOrderList($ids);

        $excelFile =  Excel::raw(new OrdersExport($ids), \Maatwebsite\Excel\Excel::XLSX);
        return response($excelFile)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->header('Content-Disposition', 'attachment; filename="orders.xlsx"')
            ->header('X-File-Name', 'orders.xlsx')
            ->header('Access-Control-Expose-Headers', 'X-File-Name');
    }

    public function import()
    {
        $excel = $this->verifyExcel();

        try {
            Excel::import(new OrdersImport(), $excel);
        } catch (\Exception $e) {
            throw new BusinessException(CodeResponse::INVALID_OPERATION, '订单导入失败' . $e->getMessage());
        }

        return $this->success();
    }
}
