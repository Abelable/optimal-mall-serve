<?php

namespace App\Models;

/**
 * App\Models\ThemeZoneGoods
 *
 * @property int $id
 * @property int $theme_id 主题id
 * @property int $goods_id 商品id
 * @property string $goods_cover 商品图片
 * @property string $goods_name 商品名称
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeZoneGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeZoneGoods newQuery()
 * @method static \Illuminate\Database\Query\Builder|ThemeZoneGoods onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeZoneGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeZoneGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeZoneGoods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeZoneGoods whereGoodsCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeZoneGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeZoneGoods whereGoodsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeZoneGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeZoneGoods whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeZoneGoods whereThemeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeZoneGoods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ThemeZoneGoods withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ThemeZoneGoods withoutTrashed()
 * @mixin \Eloquent
 */
class ThemeZoneGoods extends BaseModel
{
}
