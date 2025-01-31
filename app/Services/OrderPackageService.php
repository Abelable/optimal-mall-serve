<?php

namespace App\Services;

use App\Models\OrderPackage;

class OrderPackageService extends BaseService
{
    public function create($orderId, $shipSn, $shipCode, $shipChannel)
    {
        $package = OrderPackage::new();
        $package->order_id = $orderId;
        $package->ship_channel = $shipChannel;
        $package->ship_sn = $shipSn;
        $package->ship_code = $shipCode;
        $package->save();
        return $package;
    }
}
