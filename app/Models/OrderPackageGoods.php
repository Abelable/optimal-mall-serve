<?php

namespace App\Models;

/**
 * App\Models\OrderPackageGoods
 *
 * @property int $id
 * @property int $package_id 包裹id
 * @property int $goods_id 商品id
 * @property int $goods_cover 商品图片
 * @property int $goods_number 商品数量
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackageGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackageGoods newQuery()
 * @method static \Illuminate\Database\Query\Builder|OrderPackageGoods onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackageGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackageGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackageGoods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackageGoods whereGoodsCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackageGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackageGoods whereGoodsNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackageGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackageGoods wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackageGoods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|OrderPackageGoods withTrashed()
 * @method static \Illuminate\Database\Query\Builder|OrderPackageGoods withoutTrashed()
 * @mixin \Eloquent
 */
class OrderPackageGoods extends BaseModel
{
}
