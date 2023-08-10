<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Services\HotelService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\HotelListInput;
use App\Utils\Inputs\HotelInput;

class HotelController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var HotelListInput $input */
        $input = HotelListInput::new();
        $columns = [
            'id',
            'status',
            'failure_reason',
            'name',
            'grade',
            'category_id',
            'rate',
            'created_at',
            'updated_at'
        ];
        $list = HotelService::getInstance()->getHotelList($input, $columns);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $scenic = HotelService::getInstance()->getHotelById($id);
        return $this->success($scenic);
    }

    public function add()
    {
        /** @var HotelInput $input */
        $input = HotelInput::new();
        HotelService::getInstance()->createHotel($input);
        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        /** @var HotelInput $input */
        $input = HotelInput::new();

        $hotel = HotelService::getInstance()->getHotelById($id);
        if (is_null($hotel)) {
            return $this->fail(CodeResponse::NOT_FOUND, '景点不存在');
        }

        HotelService::getInstance()->updateHotel($hotel, $input);

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        $scenic = HotelService::getInstance()->getHotelById($id);
        if (is_null($scenic)) {
            return $this->fail(CodeResponse::NOT_FOUND, '景点不存在');
        }
        $scenic->delete();

        return $this->success();
    }

    public function options()
    {
        $options = HotelService::getInstance()->getHotelOptions(['id', 'name']);
        return $this->success($options);
    }
}
