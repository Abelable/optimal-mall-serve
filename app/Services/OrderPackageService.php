<?php

namespace App\Services;

use App\Models\OrderPackage;

class OrderPackageService extends BaseService
{
    public function create($orderId, $shipChannel, $shipCode, $shipSn)
    {
        $package = OrderPackage::new();
        $package->order_id = $orderId;
        $package->ship_channel = $shipChannel;
        $package->ship_code = $shipCode;
        $package->ship_sn = $shipSn;
        $package->save();
        return $package;
    }

    public function getListByOrderId($orderId, $columns = ['*'])
    {
        return OrderPackage::with('goodsList')->where('order_id', $orderId)->get($columns);
    }

    public function getPackageById($id, $columns = ['*'])
    {
        return OrderPackage::query()->find($id, $columns);
    }
}
