<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThemeZone;
use App\Services\ThemeZoneService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\ThemeZoneInput;
use App\Utils\Inputs\PageInput;

class ThemeZoneController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $list = ThemeZoneService::getInstance()->getThemeZonePage($input);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $themeZone = ThemeZoneService::getInstance()->getThemeZoneById($id);
        if (is_null($themeZone)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前主题专区不存在');
        }
        return $this->success($themeZone);
    }

    public function add()
    {
        /** @var ThemeZoneInput $input */
        $input = ThemeZoneInput::new();
        $themeZone = ThemeZone::new();
        ThemeZoneService::getInstance()->updateThemeZone($themeZone, $input);
        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        /** @var ThemeZoneInput $input */
        $input = ThemeZoneInput::new();

        $themeZone = ThemeZoneService::getInstance()->getThemeZoneById($id);
        if (is_null($themeZone)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前活动zone不存在');
        }

        ThemeZoneService::getInstance()->updateThemeZone($themeZone, $input);

        return $this->success();
    }

    public function editSort() {
        $id = $this->verifyRequiredId('id');
        $sort = $this->verifyRequiredInteger('sort');

        $themeZone = ThemeZoneService::getInstance()->getThemeZoneById($id);
        if (is_null($themeZone)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前主题专区不存在');
        }

        $themeZone->sort = $sort;
        $themeZone->save();

        return $this->success();
    }

    public function editStatus() {
        $id = $this->verifyRequiredId('id');
        $status = $this->verifyRequiredInteger('status');

        $themeZone = ThemeZoneService::getInstance()->getThemeZoneById($id);
        if (is_null($themeZone)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前主题专区不存在');
        }

        $themeZone->status = $status;
        $themeZone->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $themeZone = ThemeZoneService::getInstance()->getThemeZoneById($id);
        if (is_null($themeZone)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前主题专区不存在');
        }
        $themeZone->delete();
        return $this->success();
    }

    public function options()
    {
        $options = ThemeZoneService::getInstance()->getThemeZoneOptions();
        return $this->success($options);
    }
}
