<?php

namespace App\Imports;

use App\Services\OrderService;
use Maatwebsite\Excel\Concerns\ToModel;

class OrdersImport implements ToModel
{
    private $rowCount = 0;

    public function model(array $row)
    {
        if ($this->rowCount === 0) {
            $this->rowCount++;
            return;
        }

        $formattedRow = [
            'order_id' => (int)$row[0],
            'ship_channel' => (string)$row[8],
            'ship_code' => (string)$row[9],
            'ship_sn' => (string)$row[10],
        ];
        OrderService::getInstance()->importOrders($formattedRow);
    }
}
