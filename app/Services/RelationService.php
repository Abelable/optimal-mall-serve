<?php

namespace App\Services;

use App\Models\Relation;

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

    public function getRelationByFanId($fanId, $columns = ['*'])
    {
        return Relation::query()->where('fan_id', $fanId)->first($columns);
    }

    public function getRelationListByFanIds(array $fanIds, $columns = ['*'])
    {
        return Relation::query()->whereIn('fan_id', $fanIds)->get($columns);
    }
}
