<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;

class OrdersImport implements ToModel
{
    protected $orderService;

    public function __construct($orderService)
    {
        $this->orderService = $orderService;
    }

    public function model(array $row)
    {
    }

    public function onRow($row)
    {
        $this->orderService->importOrders([$row]);
    }

}
