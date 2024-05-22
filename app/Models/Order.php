<?php

namespace App\Models;

use App\Utils\Traits\OrderStatusTrait;

class Order extends BaseModel
{
    use OrderStatusTrait;
}
