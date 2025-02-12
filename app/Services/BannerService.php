<?php

namespace App\Services;

use App\Models\Banner;
use App\Utils\Inputs\Admin\BannerInput;
use App\Utils\Inputs\BannerPageInput;

class BannerService extends BaseService
{
    public function updateBanner(Banner $banner, BannerInput $input)
    {
        $banner->cover = $input->cover;
        if (!is_null($input->desc)) {
            $banner->desc = $input->desc;
        }
        if (!is_null($input->scene)) {
            $banner->scene = $input->scene;
            $banner->param = $input->param;
        }
        $banner->position = $input->position;
        $banner->save();
        return $banner;
    }

    public function getBannerPage(BannerPageInput $input, $columns = ['*'])
    {
        $query = Banner::query()->orderByRaw("FIELD(status, 1, 2)");
        if (!is_null($input->position)) {
            $query->where('position', $input->position);
        }
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
        return Banner::query()->find($id, $columns);
    }

    public function getPop($columns = ['*'])
    {
        return Banner::query()
            ->where('position', 3)
            ->where('status', 1)
            ->orderBy('sort', 'desc')
            ->orderBy('created_at', 'desc')
            ->first($columns);
    }

    public function getBannerList($position = 1, $columns = ['*'])
    {
        return Banner::query()
            ->where('position', $position)
            ->where('status', 1)
            ->orderBy('sort', 'desc')
            ->orderBy('created_at', 'desc')
            ->get($columns);
    }
}
