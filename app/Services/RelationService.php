<?php

namespace App\Services;

use App\Models\Relation;
use Illuminate\Support\Carbon;

class RelationService extends BaseService
{
    public function banding($superiorId, $fanId)
    {
        $relation = Relation::new();
        $relation->superior_id = $superiorId;
        $relation->fan_id = $fanId;
        $relation->save();
        return $relation;
    }

    public function getListByFanIds(array $fanIds, $columns = ['*'])
    {
        return Relation::query()->whereIn('fan_id', $fanIds)->get($columns);
    }

    public function getListBySuperiorId($superiorId, $columns = ['*'])
    {
        return Relation::query()->where('superior_id', $superiorId)->get($columns);
    }

    public function getRelationListBySuperiorIds(array $superiorIds, $columns = ['*'])
    {
        return Relation::query()->whereIn('superior_id', $superiorIds)->get($columns);
    }

    public function getCountBySuperiorId($superiorId)
    {
        return Relation::query()->where('superior_id', $superiorId)->count();
    }

    public function getTodayCountBySuperiorId($superiorId)
    {
        return Relation::query()->whereDate('created_at', Carbon::today())->where('superior_id', $superiorId)->count();
    }

    public function getTodayListBySuperiorId($superiorId, $columns = ['*'])
    {
        return Relation::query()->whereDate('created_at', Carbon::today())->where('superior_id', $superiorId)->get($columns);
    }

    public function getSuperiorId($fanId)
    {
        $relation = Relation::query()->where('fan_id', $fanId)->first();
        return $relation->superior_id ?? null;
    }
}
