<?php

namespace App\Imports;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Database\Eloquent\Model;
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
        // TODO: Implement model() method.
    }

    public function onRow($row)
    {
        $this->orderService->importOrders([$row]);
    }

}
