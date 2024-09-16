<?php

namespace App\Models;

use Laravel\Scout\Searchable;

/**
 * App\Models\Goods
 *
 * @property int $id
 * @property int $status 商品状态：1-销售中，2-下架
 * @property int $merchant_id 商家id
 * @property string $video 商品视频
 * @property string $cover 商品封面
 * @property string $activity_cover 活动封面
 * @property string $image_list 主图图片列表
 * @property string $detail_image_list 详情图片列表
 * @property string $default_spec_image 默认规格图片
 * @property string $name 商品名称
 * @property string $introduction 商品介绍
 * @property int $freight_template_id 运费模板id：0-包邮
 * @property float $price 起始价格
 * @property float $market_price 市场原价
 * @property int $stock 商品库存
 * @property float $commission_rate 佣金比例%
 * @property int $refund_status 是否支持7天无理由：0-不支持，1-支持
 * @property string $spec_list 商品规格列表
 * @property string $sku_list 商品sku
 * @property int $sales_volume 商品销量
 * @property int $avg_score 综合评分
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\GoodsCategory[] $categories
 * @property-read int|null $categories_count
 * @property-read \App\Models\FreightTemplate|null $freightTemplateInfo
 * @method static \Illuminate\Database\Eloquent\Builder|Goods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Goods newQuery()
 * @method static \Illuminate\Database\Query\Builder|Goods onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Goods query()
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereActivityCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereAvgScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereDefaultSpecImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereDetailImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereFreightTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereIntroduction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereMarketPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereRefundStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereSalesVolume($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereSkuList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereSpecList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereVideo($value)
 * @method static \Illuminate\Database\Query\Builder|Goods withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Goods withoutTrashed()
 * @mixin \Eloquent
 */
class Goods extends BaseModel
{
    use Searchable;

    /**
     * 索引的字段
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return $this->only('id', 'name');
    }

    public function categories()
    {
        return $this->hasMany(GoodsCategory::class, 'goods_id');
    }

    public function freightTemplateInfo()
    {
        return $this->belongsTo(FreightTemplate::class, 'freight_template_id');
    }
}
