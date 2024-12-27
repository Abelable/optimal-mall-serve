<?php

namespace App\Services;

use App\Models\Express;
use App\Utils\Inputs\ExpressPageInput;

class ExpressService extends BaseService
{
    public function getExpressList(ExpressPageInput $input, $columns = ['*'])
    {
        $query = Express::query();
        if (!empty($input->name)) {
            $query = $query->where('name', 'like', '%' . $input->name . '%');
        }
        if (!empty($input->code)) {
            $query = $query->where('code', 'like', '%' . $input->code . '%');
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getExpressById($id, $columns = ['*'])
    {
        return Express::query()->find($id, $columns);
    }

    public function getExpressByCode($code, $columns = ['*'])
    {
        return Express::query()->where('code', $code)->first($columns);
    }

    public function getExpressByName($name, $columns = ['*'])
    {
        return Express::query()->where('name', $name)->first($columns);
    }

    public function getExpressOptions($columns = ['*'])
    {
        return Express::query()->get($columns);
    }
}
