<?php

namespace App\Services;

use App\Models\ProviderScenicSpot;
use App\Utils\Inputs\Admin\ProviderScenicListInput;
use App\Utils\Inputs\ProviderScenicSpotListInput;

class ProviderScenicSpotService extends BaseService
{
    public function getListTotal($userId, $status)
    {
        return ProviderScenicSpot::query()->where('user_id', $userId)->where('status', $status)->count();
    }

    public function getUserSpotList($userId, ProviderScenicSpotListInput $input, $columns = ['*'])
    {
        return ProviderScenicSpot::query()
            ->where('user_id', $userId)
            ->where('status', $input->status)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getUserSpotById($userId, $id, $columns = ['*'])
    {
        return ProviderScenicSpot::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function getSpotByScenicId($userId, $scenicId, $columns = ['*'])
    {
        return ProviderScenicSpot::query()->where('user_id', $userId)->where('scenic_id', $scenicId)->first($columns);
    }

    public function getScenicList(ProviderScenicListInput $input, $columns = ['*'])
    {
        $query = ProviderScenicSpot::query();
        if (!is_null($input->status)) {
            $query = $query->where('status', $input->status);
        }
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getScenicById($id, $columns = ['*'])
    {
        return ProviderScenicSpot::query()->find($id, $columns);
    }
}
