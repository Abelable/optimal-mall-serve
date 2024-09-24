<?php

namespace App\Models;

/**
 * App\Models\WxSubscriptionMessage
 *
 * @property int $id
 * @property string $template_id 订阅模板id
 * @property string $page 跳转页面
 * @property string $open_id 接受者openid
 * @property string $data 消息模板内容
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|WxSubscriptionMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WxSubscriptionMessage newQuery()
 * @method static \Illuminate\Database\Query\Builder|WxSubscriptionMessage onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|WxSubscriptionMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|WxSubscriptionMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WxSubscriptionMessage whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WxSubscriptionMessage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WxSubscriptionMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WxSubscriptionMessage whereOpenId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WxSubscriptionMessage wherePage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WxSubscriptionMessage whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WxSubscriptionMessage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|WxSubscriptionMessage withTrashed()
 * @method static \Illuminate\Database\Query\Builder|WxSubscriptionMessage withoutTrashed()
 * @mixin \Eloquent
 */
class WxSubscriptionMessage extends BaseModel
{
}
