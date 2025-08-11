<?php

namespace App\Models;

/**
 * App\Models\LiveGoods
 *
 * @property int $id
 * @property int $room_id 直播间id
 * @property int $goods_id 商品id
 * @property int $is_hot 是否正在热卖：0-非热卖，1-热卖中
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGoods newQuery()
 * @method static \Illuminate\Database\Query\Builder|LiveGoods onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGoods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGoods whereIsHot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGoods whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGoods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|LiveGoods withTrashed()
 * @method static \Illuminate\Database\Query\Builder|LiveGoods withoutTrashed()
 * @mixin \Eloquent
 */
class LiveGoods extends BaseModel
{
}
