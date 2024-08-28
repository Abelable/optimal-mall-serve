<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Services\GoodsService;
use App\Services\ActivityService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\ActivityInput;
use App\Utils\Inputs\PageInput;

class ActivityController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $list = ActivityService::getInstance()->getActivityPage($input);
        return $this->successPaginate($list);
    }

    public function add()
    {
        /** @var ActivityInput $input */
        $input = ActivityInput::new();

        $activityList = ActivityService::getInstance()->getActivityListByGoodsIds($input->goodsIds);
        if (count($activityList) != 0) {
            return $this->fail(CodeResponse::DATA_EXISTED, '已添加相同商品');
        }

        $goodsList = GoodsService::getInstance()->getGoodsListByIds($input->goodsIds, ['id', 'cover', 'name']);

        foreach ($goodsList as $goods) {
            $activity = Activity::new();
            $activity->status = $input->status;
            if (!is_null($input->startTime)) {
                $activity->start_time = $input->startTime;
            }
            if (!is_null($input->endTime)) {
                $activity->end_time = $input->endTime;
            }
            $activity->goods_type = $input->goodsType;
            $activity->goods_id = $goods->id;
            $activity->goods_cover = $goods->cover;
            $activity->goods_name = $goods->name;
            $activity->save();
        }

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $activity = ActivityService::getInstance()->getActivityById($id);
        if (is_null($activity)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }
        $activity->delete();
        return $this->success();
    }
}
