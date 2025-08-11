<?php

namespace App\Models;

/**
 * App\Models\Fan
 *
 * @property int $id
 * @property int $author_id 作者id
 * @property int $fan_id 粉丝id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Fan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Fan newQuery()
 * @method static \Illuminate\Database\Query\Builder|Fan onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Fan query()
 * @method static \Illuminate\Database\Eloquent\Builder|Fan whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fan whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fan whereFanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Fan withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Fan withoutTrashed()
 * @mixin \Eloquent
 */
class Fan extends BaseModel
{
}
