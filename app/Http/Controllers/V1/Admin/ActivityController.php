<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ActivityEnd;
use App\Jobs\ActivityStart;
use App\Models\Activity;
use App\Services\GoodsService;
use App\Services\ActivityService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\ActivityPageInput;
use App\Utils\Inputs\Admin\ActivityEditInput;
use App\Utils\Inputs\Admin\ActivityInput;

class ActivityController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var ActivityPageInput $input */
        $input = ActivityPageInput::new();
        $list = ActivityService::getInstance()->getActivityPage($input);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $activity = ActivityService::getInstance()->getActivityById($id, ['id', 'status', 'name', 'start_time', 'end_time', 'goods_tag']);
        if (is_null($activity)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前活动不存在');
        }
        return $this->success($activity);
    }

    public function add()
    {
        /** @var ActivityInput $input */
        $input = ActivityInput::new();

        $activityList = ActivityService::getInstance()->getActivityListByGoodsIds($input->goodsIds);
        if (count($activityList) != 0) {
            return $this->fail(CodeResponse::DATA_EXISTED, '已添加相同活动');
        }

        $goodsList = GoodsService::getInstance()->getGoodsListByIds($input->goodsIds, ['id', 'cover', 'name']);

        foreach ($goodsList as $goods) {
            $activity = Activity::new();
            $activity->name = $input->name;
            $activity->status = $input->status;
            if (!is_null($input->startTime)) {
                $activity->start_time = $input->startTime;
            }
            if (!is_null($input->endTime)) {
                $activity->end_time = $input->endTime;
            }
            $activity->goods_id = $goods->id;
            $activity->goods_cover = $goods->cover;
            $activity->goods_name = $goods->name;
            $activity->save();

            if (!is_null($input->startTime)) {
                $this->dispatch(new ActivityStart($activity->id, $input->startTime));
            }
            if (!is_null($input->endTime)) {
                $this->dispatch(new ActivityEnd($activity->id, $input->endTime));
            }
        }

        return $this->success();
    }

    public function edit()
    {
        /** @var ActivityEditInput $input */
        $input = ActivityEditInput::new();

        $activity = ActivityService::getInstance()->getActivityById($input->id);
        if (is_null($activity)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前活动不存在');
        }

        $activity->name = $input->name;
        $activity->status = $input->status;
        if (!is_null($input->startTime)) {
            $activity->start_time = $input->startTime;
        }
        if (!is_null($input->endTime)) {
            $activity->end_time = $input->endTime;
        }
        $activity->save();

        return $this->success();
    }

    public function editTag()
    {
        $id = $this->verifyRequiredId('id');
        $tag = $this->verifyRequiredInteger('tag');

        $activity = ActivityService::getInstance()->getActivityById($id);
        if (is_null($activity)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前活动不存在');
        }

        $activity->tag = $tag;
        $activity->save();

        return $this->success();
    }

    public function editGoodsTag()
    {
        $id = $this->verifyRequiredId('id');
        $goodsTag = $this->verifyRequiredInteger('goodsTag');

        $activity = ActivityService::getInstance()->getActivityById($id);
        if (is_null($activity)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前活动不存在');
        }

        $activity->goods_tag = $goodsTag;
        $activity->save();

        return $this->success();
    }

    public function editFollowers()
    {
        $id = $this->verifyRequiredId('id');
        $followers = $this->verifyRequiredInteger('followers');

        $activity = ActivityService::getInstance()->getActivityById($id);
        if (is_null($activity)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前活动不存在');
        }

        $activity->followers = $followers;
        $activity->save();

        return $this->success();
    }

    public function editSales()
    {
        $id = $this->verifyRequiredId('id');
        $sales = $this->verifyRequiredInteger('sales');

        $activity = ActivityService::getInstance()->getActivityById($id);
        if (is_null($activity)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前活动不存在');
        }

        $activity->sales = $sales;
        $activity->save();

        return $this->success();
    }

    public function end()
    {
        $id = $this->verifyRequiredId('id');
        $activity = ActivityService::getInstance()->getActivityById($id);
        if (is_null($activity)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前活动不存在');
        }
        $activity->status = 2;
        $activity->save();
        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $activity = ActivityService::getInstance()->getActivityById($id);
        if (is_null($activity)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前活动不存在');
        }
        $activity->delete();
        return $this->success();
    }
}
