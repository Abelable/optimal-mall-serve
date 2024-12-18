<?php

namespace App\Models;

class OrderGoods extends BaseModel
{
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
