<?php

namespace App\Models;

/**
 * App\Models\OrderKeyword
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property string $content 搜索关键字内容
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|OrderKeyword newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderKeyword newQuery()
 * @method static \Illuminate\Database\Query\Builder|OrderKeyword onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderKeyword query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderKeyword whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderKeyword whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderKeyword whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderKeyword whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderKeyword whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderKeyword whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|OrderKeyword withTrashed()
 * @method static \Illuminate\Database\Query\Builder|OrderKeyword withoutTrashed()
 * @mixin \Eloquent
 */
class OrderKeyword extends BaseModel
{
}
