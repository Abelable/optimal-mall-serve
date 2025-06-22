<?php

namespace App\Http\Controllers\V1\Admin;

use App\Exceptions\BusinessException;
use App\Exports\OrdersExport;
use App\Http\Controllers\Controller;
use App\Imports\OrdersImport;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Services\ExpressService;
use App\Services\GoodsService;
use App\Services\MerchantPickupAddressService;
use App\Services\OrderGoodsService;
use App\Services\OrderPackageGoodsService;
use App\Services\OrderPackageService;
use App\Services\OrderService;
use App\Services\OrderVerifyService;
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

            $goodsList = [];
            if (!is_null($groupedGoodsList->get($order->id))) {
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
            }
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

        $packageGoodsList = OrderPackageGoodsService::getInstance()->getListByOrderId($order->id);
        $order['package_goods_list'] = $packageGoodsList ?: [];

        $packageList = OrderPackageService::getInstance()->getListByOrderId($order->id);
        $order['package_list'] = $packageList ?: [];

        if ($order->delivery_mode == 2) {
            $pickupAddress = MerchantPickupAddressService::getInstance()
                ->getAddressById($order->pickup_address_id, ['id', 'name', 'address_detail', 'latitude', 'longitude']);
            $order['pickup_address'] = $pickupAddress;
            unset($order['pickup_address_id']);

            $verifyInfo = OrderVerifyService::getInstance()->getByOrderId($order->id);
            $order['verify_code'] = $verifyInfo->verify_code ?: null;
        }

        return $this->success($order);
    }

    public function delivery()
    {
        $id = $this->verifyRequiredInteger('id');
        $packageList = $this->verifyArrayNotEmpty('packageList');
        $isAllDelivered = $this->verifyRequiredInteger('isAllDelivered');

        OrderService::getInstance()->splitShip($id, $packageList, $isAllDelivered == 1);

        // todo: 管理员操组记录

        return $this->success();
    }

    public function shippingInfo()
    {
        $id = $this->verifyRequiredId('id');
        $package = OrderPackageService::getInstance()->getPackageById($id);

        if (!is_null($package)) {
            $order = OrderService::getInstance()->getOrderById($package->order_id);
            if (is_null($order)) {
                return $this->fail(CodeResponse::NOT_FOUND, '订单不存在');
            }
            $traces = ExpressServe::new()->track($package->ship_code, $package->ship_sn, $order->mobile);
        } else {
            // todo 旧逻辑兼容处理
            $order = OrderService::getInstance()->getOrderById($id);
            if (is_null($order)) {
                return $this->fail(CodeResponse::NOT_FOUND, '订单不存在');
            }
            $traces = ExpressServe::new()->track($order->ship_code, $order->ship_sn, $order->mobile);
        }

        return $this->success([
            'shipChannel' => $package ? $package->ship_channel : $order->ship_channel,
            'shipSn' => $package ? $package->ship_sn : $order->ship_sn,
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

    public function modifyAddressInfo()
    {
        $id = $this->verifyRequiredInteger('id');
        $consignee = $this->verifyRequiredString('consignee');
        $mobile = $this->verifyRequiredString('mobile');
        $address = $this->verifyRequiredString('address');

        $order = OrderService::getInstance()->getOrderById($id);
        if (!$order->canShipHandle()) {
            return $this->fail(CodeResponse::ORDER_INVALID_OPERATION, '非待发货订单，无法修改地址');
        }
        $order->consignee = $consignee;
        $order->mobile = $mobile;
        $order->address = $address;
        $order->save();

        return $this->success();
    }

    public function modifyDeliveryInfo()
    {
        $id = $this->verifyRequiredInteger('id');
        $packageList = $this->verifyArrayNotEmpty('packageList');

        DB::transaction(function () use ($id, $packageList) {
            OrderPackageService::getInstance()->deleteListByOrderId($id);
            OrderPackageGoodsService::getInstance()->deleteListByOrderId($id);

            foreach ($packageList as $package) {
                $shipChannel = $package['shipChannel'];
                $shipCode = $package['shipCode'];
                $shipSn = $package['shipSn'];
                if (empty($shipCode)) {
                    $express = ExpressService::getInstance()->getExpressByName($shipChannel);
                    $shipCode = $express->code;
                }
                $orderPackage = OrderPackageService::getInstance()->create($id, $shipChannel, $shipCode, $shipSn);

                $goodsList = json_decode($package['goodsList']);
                foreach ($goodsList as $goods) {
                    OrderPackageGoodsService::getInstance()
                        ->create($id, $orderPackage->id, $goods->goodsId, $goods->cover, $goods->name, $goods->selectedSkuName, $goods->number);
                }
            }
        });

        return $this->success();
    }

    public function updateOrderGoodsStatus()
    {
        $orderList = Order::query()->get();
        $orderList->map(function (Order $order) {
            $orderGoodsList = OrderGoodsService::getInstance()->getListByOrderId($order->id);
            if ($order->status == 201
                || $order->status == 204
                || $order->status == 301
                || $order->status == 401
                || $order->status == 402
                || $order->status == 403
                || $order->status == 501
                || $order->status == 502
            ) {
                $orderGoodsList->map(function (OrderGoods $orderGoods) {
                    $orderGoods->status = 1;
                    $orderGoods->save();
                });
            } else if ($order->status == 202 || $order->status == 203) {
                $orderGoodsList->map(function (OrderGoods $orderGoods) {
                    $orderGoods->status = 2;
                    $orderGoods->save();
                });
            }
        });
        return $this->success();
    }
}
