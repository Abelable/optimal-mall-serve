<?php

namespace App\Utils\Traits;


use App\Exceptions\BusinessException;
use App\Utils\CodeResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use function request;

trait VerifyRequestInput
{
    public function verifyId($key, $default = null)
    {
        return $this->verifyData($key, $default, 'integer|digits_between:1,20');
    }

    public function verifyRequiredId($key, $default = null)
    {
        return $this->verifyData($key, $default, 'required|integer|digits_between:1,20');
    }

    public function verifyString($key, $default = null)
    {
        return $this->verifyData($key, $default, 'string');
    }

    public function verifyRequiredString($key, $default = null)
    {
        return $this->verifyData($key, $default, 'required|string');
    }

    public function verifyBoolean($key, $default = null)
    {
        return $this->verifyData($key, $default, 'boolean');
    }

    public function verifyInteger($key, $default = null)
    {
        return $this->verifyData($key, $default, 'integer');
    }

    public function verifyRequiredInteger($key, $default = null)
    {
        return $this->verifyData($key, $default, 'required|integer');
    }

    public function verifyPositiveInteger($key, $default = null)
    {
        return $this->verifyData($key, $default, 'integer|min:1');
    }

    public function verifyEnum($key, $default = null, $enum = [])
    {
        return $this->verifyData($key, $default, Rule::in($enum));
    }

    public function verifyArrayNotEmpty($key, $default = null)
    {
        return $this->verifyData($key, $default, 'array|min:1');
    }

    public function verifyArray($key, $default = null)
    {
        return $this->verifyData($key, $default, 'array');
    }

    public function verifyMobile()
    {
        return $this->verifyData('mobile', null, 'required|regex:/^1[345789][0-9]{9}$/');
    }

    public function verifyExcel()
    {
        return $this->verifyData('excel', null, 'required|file|mimes:xlsx,xls|max:2048', 'file');
    }

    /**
     * @throws BusinessException
     */
    public function verifyData($key, $default, $rule, $type = 'data')
    {
        if ($type == 'file') {
            $value = request()->file($key, $default);
        } else {
            $value = request()->input($key, $default);
        }
        $validator = Validator::make([$key => $value], [$key => $rule]);
        if (is_null($default) && is_null($value)) {
            return null;
        }
        if ($validator->fails()) {
            throw new BusinessException(CodeResponse::PARAM_VALUE_INVALID);
        }
        return $value;
    }
}
