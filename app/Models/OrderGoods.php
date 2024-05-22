<?php

namespace App\Models;

/**
 * App\Models\OrderGoods
 *
 * @property int $id
 * @property int $order_id 订单id
 * @property int $goods_id 商品id
 * @property string $cover 列表图片
 * @property string $name 商品名称
 * @property float $price 商品价格
 * @property string $selected_sku_name 选中的规格名称
 * @property int $selected_sku_index 选中的规格索引
 * @property int $number 商品数量
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods newQuery()
 * @method static \Illuminate\Database\Query\Builder|OrderGoods onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods whereSelectedSkuIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods whereSelectedSkuName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|OrderGoods withTrashed()
 * @method static \Illuminate\Database\Query\Builder|OrderGoods withoutTrashed()
 * @mixin \Eloquent
 */
class OrderGoods extends BaseModel
{
}
