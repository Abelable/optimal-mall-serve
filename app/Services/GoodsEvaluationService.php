<?php

namespace App\Services;

use App\Models\GoodsEvaluation;
use App\Utils\Inputs\GoodsEvaluationInput;
use App\Utils\Inputs\PageInput;

class GoodsEvaluationService extends BaseService
{
    public function evaluationPage($goodsId, PageInput $input, $columns = ['*'])
    {
        return GoodsEvaluation::query()
            ->where('goods_id', $goodsId)
            ->orderBy('like_number', 'desc')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getUserEvaluation($userId, $id, $columns = ['*'])
    {
        return GoodsEvaluation::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function getEvaluationByOrderId($orderId, $columns = ['*'])
    {
        return GoodsEvaluation::query()->where('order_id', $orderId)->first($columns);
    }

    public function createEvaluation($userId, GoodsEvaluationInput $input)
    {
        foreach ($input->goodsIds as $goodsId) {
            $evaluation = GoodsEvaluation::new();
            $evaluation->user_id = $userId;
            $evaluation->goods_id = $goodsId;
            $evaluation->score = $input->score;
            $evaluation->content = $input->content;
            $evaluation->image_list = json_encode($input->imageList);
            $evaluation->save();

            $avgScore = $this->getAverageScore($goodsId);
            GoodsService::getInstance()->updateAvgScore($goodsId, round($avgScore, 1));
        }
    }

    public function editEvaluation($userId, GoodsEvaluationInput $input)
    {

    }

    public function getAverageScore($goodsId)
    {
        return GoodsEvaluation::query()->where('goods_id', $goodsId)->avg('score');
    }

    public function getTotalNum($goodsId)
    {
        return GoodsEvaluation::query()->where('goods_id', $goodsId)->count();
    }

    public function evaluationList($goodsId, $count, $columns = ['*'])
    {
        return GoodsEvaluation::query()
            ->where('goods_id', $goodsId)
            ->orderBy('like_number', 'desc')
            ->orderBy('created_at', 'desc')
            ->take($count)
            ->get($columns);
    }
}
