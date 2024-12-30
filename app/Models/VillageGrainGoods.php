<?php

namespace App\Models;

/**
 * App\Models\VillageGrainGoods
 *
 * @property int $id
 * @property int $goods_id 商品id
 * @property string $goods_cover 商品图片
 * @property string $goods_name 商品名称
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|VillageGrainGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VillageGrainGoods newQuery()
 * @method static \Illuminate\Database\Query\Builder|VillageGrainGoods onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|VillageGrainGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|VillageGrainGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VillageGrainGoods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VillageGrainGoods whereGoodsCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VillageGrainGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VillageGrainGoods whereGoodsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VillageGrainGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VillageGrainGoods whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VillageGrainGoods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|VillageGrainGoods withTrashed()
 * @method static \Illuminate\Database\Query\Builder|VillageGrainGoods withoutTrashed()
 * @mixin \Eloquent
 */
class VillageGrainGoods extends BaseModel
{
}
