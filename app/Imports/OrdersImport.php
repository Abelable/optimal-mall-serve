<?php

namespace App\Imports;

use App\Models\Order;
use App\Services\OrderService;
use Maatwebsite\Excel\Concerns\ToModel;

class OrdersImport implements ToModel
{
    protected $orderService;

    public function __construct($orderService)
    {
        $this->orderService = $orderService;
    }

    /**
    * @param array $row
    *
    * @return array
     */
    public function model(array $row)
    {
        return $row;
    }

    public function onRow($row)
    {
        $this->orderService->importOrders([$row]);
    }
}
