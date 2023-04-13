<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScenicSpot;
use App\Services\ScenicProjectService;
use App\Services\ScenicProviderService;
use App\Services\ScenicService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\ScenicListInput;
use App\Utils\Inputs\ScenicAddInput;
use App\Utils\Inputs\ScenicEditInput;
use Illuminate\Support\Facades\DB;

class ScenicController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var ScenicListInput $input */
        $input = ScenicListInput::new();
        $columns = [
            'id',
            'status',
            'failure_reason',
            'name',
            'level',
            'category_id',
            'rate',
            'created_at',
            'updated_at'
        ];
        $list = ScenicService::getInstance()->getScenicList($input, $columns);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');

        $scenic = ScenicService::getInstance()->getScenicById($id);
        if (is_null($scenic)) {
            return $this->fail(CodeResponse::NOT_FOUND, '景点不存在');
        }

        $scenic->policy_list = json_decode($scenic->policy_list);
        $scenic->hotline_list = json_decode($scenic->hotline_list);
        $scenic->facility_list = json_decode($scenic->facility_list);
        $scenic->tips_list = json_decode($scenic->tips_list);

        return $this->success($scenic);
    }

    public function add()
    {
        /** @var ScenicAddInput $input */
        $input = ScenicAddInput::new();

        DB::transaction(function () use ($input) {
            $scenic = ScenicSpot::new();
            $scenic->status = 1;
            $scenic->name = $input->name;
            $scenic->category_id = $input->categoryId;
            if (!empty($input->video)) {
                $scenic->video = $input->video;
            }
            $scenic->image_list = json_encode($input->imageList);
            $scenic->latitude = $input->latitude;
            $scenic->longitude = $input->longitude;
            $scenic->address = $input->address;
            $scenic->policy_list = json_encode($input->policy_list);
            $scenic->hotline_list = json_encode($input->hotline_list);
            $scenic->facility_list = json_encode($input->facility_list);
            $scenic->tips_list = json_encode($input->tips_list);
            $scenic->save();

            if (count($input->project_list) != 0) {
                $projectList = array_map(function ($item) use ($scenic) {
                    $item['scenic_id'] = $scenic->id;
                }, $input->project_list);
                ScenicProjectService::getInstance()->insert($projectList);
            }
        });

        return $this->success();
    }

    public function edit()
    {
        /** @var ScenicEditInput $input */
        $input = ScenicEditInput::new();

        $scenic = ScenicService::getInstance()->getScenicById($input->id);
        if (is_null($scenic)) {
            return $this->fail(CodeResponse::NOT_FOUND, '景点不存在');
        }

        if (!empty($input->video)) {
            $scenic->video = $input->video;
        }
        $scenic->image_list = $input->imageList;
        $scenic->name = $input->name;
        $scenic->category_id = $input->categoryId;
        $scenic->latitude = $input->latitude;
        $scenic->longitude = $input->longitude;
        $scenic->address = $input->address;
        $scenic->policy_list = json_encode($input->policy_list);
        $scenic->hotline_list = json_encode($input->hotline_list);
        $scenic->facility_list = json_encode($input->facility_list);
        $scenic->tips_list = json_encode($input->tips_list);

        $scenic->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        $scenic = ScenicService::getInstance()->getScenicById($id);
        if (is_null($scenic)) {
            return $this->fail(CodeResponse::NOT_FOUND, '景点不存在');
        }
        $scenic->delete();

        return $this->success();
    }
}
