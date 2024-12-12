<?php

namespace App\Services;

use App\Models\IntegrityBanner;
use App\Utils\Inputs\Admin\BannerInput;
use App\Utils\Inputs\BannerPageInput;

class IntegrityBannerService extends BaseService
{
    public function updateBanner(IntegrityBanner $banner, BannerInput $input)
    {
        $banner->cover = $input->cover;
        if (!is_null($input->desc)) {
            $banner->desc = $input->desc;
        }
        if (!is_null($input->scene)) {
            $banner->scene = $input->scene;
            $banner->param = $input->param;
        }
        $banner->save();
        return $banner;
    }

    public function getBannerPage(BannerPageInput $input, $columns = ['*'])
    {
        $query = IntegrityBanner::query();
        if (!is_null($input->status)) {
            $query->where('status', $input->status);
        }
        if (!is_null($input->scene)) {
            $query->where('scene', $input->scene);
        }
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getBannerById($id, $columns = ['*'])
    {
        return IntegrityBanner::query()->find($id, $columns);
    }

    public function getBannerList($columns = ['*'])
    {
        return IntegrityBanner::query()
            ->where('status', 1)
            ->orderBy('sort', 'desc')
            ->orderBy('created_at', 'desc')
            ->get($columns);
    }
}
