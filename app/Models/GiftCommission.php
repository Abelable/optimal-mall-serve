<?php

namespace App\Models;

/**
 * App\Models\GiftCommission
 *
 * @property int $id
 * @property int $status 佣金状态：0-订单待支付，1-待结算, 2-可提现，3-已结算
 * @property int $user_id 用户id
 * @property int $superior_id 上级id
 * @property int $order_id 订单id
 * @property int $goods_id 商品id
 * @property int $refund_status 是否支持7天无理由：0-不支持，1-支持
 * @property string $selected_sku_name 选中的规格名称
 * @property float $goods_price 商品价格
 * @property float $commission 佣金金额
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission newQuery()
 * @method static \Illuminate\Database\Query\Builder|GiftCommission onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission query()
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission whereCommission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission whereGoodsPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission whereRefundStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission whereSelectedSkuName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission whereSuperiorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|GiftCommission withTrashed()
 * @method static \Illuminate\Database\Query\Builder|GiftCommission withoutTrashed()
 * @mixin \Eloquent
 */
class GiftCommission extends BaseModel
{
}
