<?php

namespace App\Models;

/**
 * App\Models\IntegrityGoods
 *
 * @property int $id
 * @property int $goods_id 商品id
 * @property string $goods_cover 商品图片
 * @property string $goods_name 商品名称
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrityGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrityGoods newQuery()
 * @method static \Illuminate\Database\Query\Builder|IntegrityGoods onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrityGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrityGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrityGoods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrityGoods whereGoodsCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrityGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrityGoods whereGoodsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrityGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrityGoods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|IntegrityGoods withTrashed()
 * @method static \Illuminate\Database\Query\Builder|IntegrityGoods withoutTrashed()
 * @mixin \Eloquent
 */
class IntegrityGoods extends BaseModel
{
}
