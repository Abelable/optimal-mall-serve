<?php

namespace App\Models;

/**
 * App\Models\OrderPackage
 *
 * @property int $id
 * @property int $order_id 订单id
 * @property string $ship_channel 快递公司
 * @property string $ship_code 快递公司编号
 * @property string $ship_sn 快递编号
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OrderPackageGoods[] $goodsList
 * @property-read int|null $goods_list_count
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackage newQuery()
 * @method static \Illuminate\Database\Query\Builder|OrderPackage onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackage query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackage whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackage whereShipChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackage whereShipCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackage whereShipSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|OrderPackage withTrashed()
 * @method static \Illuminate\Database\Query\Builder|OrderPackage withoutTrashed()
 * @mixin \Eloquent
 * @property-read \App\Models\Order|null $order
 */
class OrderPackage extends BaseModel
{
    public function goodsList()
    {
        return $this->hasMany(OrderPackageGoods::class, 'package_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
