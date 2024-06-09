<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\FreightTemplate;
use App\Services\FreightTemplateService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\NamePageInput;
use App\Utils\Inputs\FreightTemplateInput;

class FreightTemplateController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var NamePageInput $input */
        $input = NamePageInput::new();
        $page = FreightTemplateService::getInstance()->getFreightTemplateList($input);
        return $this->successPaginate($page);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $detail = FreightTemplateService::getInstance()->getFreightTemplateById($id);
        $detail->area_list = json_decode($detail->area_list);
        return $this->success($detail);
    }

    public function add()
    {
        /** @var FreightTemplateInput $input */
        $input = FreightTemplateInput::new();
        $freightTemplate = FreightTemplate::new();
        $this->update($freightTemplate, $input);

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        /** @var FreightTemplateInput $input */
        $input = FreightTemplateInput::new();

        $freightTemplate = FreightTemplateService::getInstance()->getFreightTemplateById($id);
        if (is_null($freightTemplate)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前运费模板不存在');
        }

        $this->update($freightTemplate, $input);

        return $this->success();
    }

    private function update($freightTemplate, FreightTemplateInput $input)
    {
        $freightTemplate->name = $input->name;
        $freightTemplate->title = $input->title;
        $freightTemplate->compute_mode = $input->computeMode;
        if (!is_null($input->freeQuota)) {
            $freightTemplate->free_quota = $input->freeQuota;
        }
        $freightTemplate->area_list = json_encode($input->areaList);
        $freightTemplate->save();

        return $freightTemplate;
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $freightTemplate = FreightTemplateService::getInstance()->getFreightTemplateById($id);
        if (is_null($freightTemplate)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前运费模板不存在');
        }
        $freightTemplate->delete();
        return $this->success();
    }

    public function options()
    {
        $options = FreightTemplateService::getInstance()->getFreightTemplateOptions();
        return $this->success($options);
    }
}
