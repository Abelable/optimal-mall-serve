<?php

namespace App\Models;

/**
 * App\Models\CartGoods
 *
 * @property int $id
 * @property int $scene 场景值：1-添加购物车，2-直接购买
 * @property int $status 购物车商品状态：1-正常状态，2-所选规格库存为0、所选规格已不存在，3-商品库存为0、商品已下架、商品已删除
 * @property string $status_desc 购物车商品状态描述
 * @property int $merchant_id 商家id
 * @property int $user_id 用户id
 * @property int $goods_id 商品id
 * @property int $is_gift 是否为礼包商品：0-否，1-是
 * @property int $refund_status 是否支持7天无理由：0-不支持，1-支持
 * @property int $freight_template_id 运费模板id
 * @property string $cover 商品图片
 * @property string $name 商品名称
 * @property string $selected_sku_name 选中的规格名称
 * @property int $selected_sku_index 选中的规格索引
 * @property float $price 商品价格
 * @property float $market_price 市场价格
 * @property float $commission_rate 分享佣金比例%
 * @property int $number_limit 商品限购数量
 * @property int $number 商品数量
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods newQuery()
 * @method static \Illuminate\Database\Query\Builder|CartGoods onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereFreightTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereIsGift($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereMarketPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereNumberLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereRefundStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereScene($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereSelectedSkuIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereSelectedSkuName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereStatusDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|CartGoods withTrashed()
 * @method static \Illuminate\Database\Query\Builder|CartGoods withoutTrashed()
 * @mixin \Eloquent
 */
class CartGoods extends BaseModel
{
}
