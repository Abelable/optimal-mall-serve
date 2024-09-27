<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\GoodsEvaluation;
use App\Services\GoodsEvaluationService;
use App\Services\GoodsService;
use App\Services\OrderService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\GoodsEvaluationInput;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\DB;

class GoodsEvaluationController extends Controller
{
    protected $except = ['summary', 'list'];

    public function summary()
    {
        $goodsId = $this->verifyRequiredId('goodsId');

        $evaluationList = GoodsEvaluationService::getInstance()->evaluationList($goodsId, 1);
        $list = $this->handelEvaluationList($evaluationList);
        $total = GoodsEvaluationService::getInstance()->getTotalNum($goodsId);
        $avgScore = GoodsEvaluationService::getInstance()->getAverageScore($goodsId);

        return $this->success([
            'list' => $list,
            'total' => $total,
            'avgScore' => $avgScore ?: 0,
        ]);
    }

    public function list()
    {
        $goodsId = $this->verifyRequiredId('goodsId');
        /** @var PageInput $input */
        $input = PageInput::new();
        $page = GoodsEvaluationService::getInstance()->evaluationPage($goodsId, $input);
        $evaluationList = collect($page->items());
        $list = $this->handelEvaluationList($evaluationList);
        return $this->success($this->paginate($page, $list));
    }

    private function handelEvaluationList($evaluationList)
    {
        $userIds = $evaluationList->pluck('user_id')->toArray();
        $userList = UserService::getInstance()->getListByIds($userIds, ['id', 'avatar', 'nickname'])->keyBy('id');

        return $evaluationList->map(function (GoodsEvaluation $evaluation) use ($userList) {
            $user = $userList->get($evaluation->user_id);
            $evaluation['userInfo'] = $user;
            $evaluation->image_list = json_decode($evaluation->image_list);
            unset($evaluation->user_id);
            unset($evaluation->goods_id);
            return $evaluation;
        });
    }

    public function add()
    {
        /** @var GoodsEvaluationInput $input */
        $input = GoodsEvaluationInput::new();

        DB::transaction(function () use ($input) {
            GoodsEvaluationService::getInstance()->createEvaluation($this->userId(), $input);

            foreach ($input->goodsIds as $goodsId) {
                $avgScore = GoodsEvaluationService::getInstance()->getAverageScore($goodsId);
                GoodsService::getInstance()->updateAvgScore($goodsId, $avgScore);
            }

            OrderService::getInstance()->finish($this->userId(), $input->orderId);
        });

        return $this->success();
    }

    public function delete()
    {
        $evaluationId = $this->verifyRequiredId('evaluationId');
        $evaluation = GoodsEvaluationService::getInstance()->getUserEvaluation($this->userId(), $evaluationId);
        if (is_null($evaluation)) {
            return $this->fail(CodeResponse::NOT_FOUND, '非本人评价，无非删除');
        }
        $evaluation->delete();
        return $this->success();
    }
}
