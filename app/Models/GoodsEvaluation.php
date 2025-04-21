<?php

namespace App\Models;

use phpDocumentor\Reflection\Types\This;

/**
 * App\Models\GoodsEvaluation
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $order_id 订单id
 * @property int $goods_id 商品id
 * @property float $score 商品评分
 * @property string $content 评论内容
 * @property string $image_list 评论图片
 * @property int $like_number 点赞数
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsEvaluation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsEvaluation newQuery()
 * @method static \Illuminate\Database\Query\Builder|GoodsEvaluation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsEvaluation query()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsEvaluation whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsEvaluation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsEvaluation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsEvaluation whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsEvaluation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsEvaluation whereImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsEvaluation whereLikeNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsEvaluation whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsEvaluation whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsEvaluation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsEvaluation whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|GoodsEvaluation withTrashed()
 * @method static \Illuminate\Database\Query\Builder|GoodsEvaluation withoutTrashed()
 * @mixin \Eloquent
 */
class GoodsEvaluation extends BaseModel
{
}
