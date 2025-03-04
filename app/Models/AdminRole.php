<?php

namespace App\Models;

/**
 * App\Models\AdminRole
 *
 * @property int $id
 * @property string $name 管理员角色名称
 * @property string $desc 管理员角色描述
 * @property string $permission 管理员角色权限
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRole newQuery()
 * @method static \Illuminate\Database\Query\Builder|AdminRole onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRole query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRole whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRole whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRole whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRole wherePermission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRole whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|AdminRole withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AdminRole withoutTrashed()
 * @mixin \Eloquent
 */
class AdminRole extends BaseModel
{
}
