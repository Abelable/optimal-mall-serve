<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersExport implements FromCollection, WithHeadings
{
    protected array $orderIds;

    public function __construct(array $orderIds)
    {
        $this->orderIds = $orderIds;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Order::with(['goodsList', 'packages.goodsList', 'merchantInfo'])
            ->whereIn('id', $this->orderIds)
            ->get()
            ->flatMap(function (Order $order) {
                if (!$order->packages || $order->packages->isEmpty()) {
                    return collect([[
                        'order_id' => $order->id,
                        'order_sn' => $order->order_sn,
                        'merchant' => $order->merchantInfo ? $order->merchantInfo->name : '自营',
                        'goods_name' => $order->goodsList->pluck('name')->implode(', '),
                        'goods_sku_name' => $order->goodsList->pluck('selected_sku_name')->implode(', '),
                        'goods_number' => $order->goodsList->pluck('number')->implode(', '),
                        'consignee' => $order->consignee,
                        'mobile' => $order->mobile,
                        'address' => $order->address,
                        'ship_channel' => $order->ship_channel,
                        'ship_code' => $order->ship_code,
                        'ship_sn' => $order->ship_sn,
                    ]]);
                }

                return $order->packages->map(function ($package) use ($order) {
                    return [
                        'order_id' => $order->id,
                        'order_sn' => $order->order_sn,
                        'merchant' => $order->merchantInfo ? $order->merchantInfo->name : '自营',
                        'goods_name' => $package->goodsList->pluck('goods_name')->implode(', '),
                        'goods_sku_name' => $package->goodsList->pluck('selected_sku_name')->implode(', '),
                        'goods_number' => $package->goodsList->pluck('goods_number')->implode(', '),
                        'consignee' => $order->consignee,
                        'mobile' => $order->mobile,
                        'address' => $order->address,
                        'ship_channel' => $package->ship_channel,
                        'ship_code' => $package->ship_code,
                        'ship_sn' => $package->ship_sn,
                    ];
                });
            });
    }

    public function headings(): array
    {
        return [
            '订单id',
            '订单编号',
            '商家',
            '商品名称',
            '商品规格',
            '商品数量',
            '收件人姓名',
            '收件人手机号',
            '收件地址',
            '快递公司',
            '快递编码',
            '物流单号'
        ];
    }
}
