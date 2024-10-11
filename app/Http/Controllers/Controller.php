<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use App\Utils\CodeResponse;
use App\Utils\Traits\VerifyRequestInput;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, VerifyRequestInput;

    protected $guard = 'user';
    protected $only;
    protected $except;

    public function __construct()
    {
        $options = [];
        if (!is_null($this->only)) {
            $options['only'] = $this->only;
        }
        if (!is_null($this->except)) {
            $options['except'] = $this->except;
        }
        $this->middleware('auth:' . $this->guard, $options);
    }

    public function isLogin()
    {
        return !is_null($this->user());
    }

    public function userId()
    {
        $userId = null;
        if (auth()->check()) {
            $userId = auth()->user()->getAuthIdentifier();
        }
        return $userId;
    }

    public function adminId()
    {
        $adminId = null;
        if (auth()->check()) {
            $adminId = $this->admin()->getAuthIdentifier();
        }
        return $adminId;
    }

    /**
     * @return User
     */
    public function user()
    {
        return Auth::guard('user')->user();
    }

    /**
     * @return Admin
     */
    public function admin()
    {
        return Auth::guard('Admin')->user();
    }


    protected function successPaginate($page)
    {
        return $this->success($this->paginate($page));
    }

    protected function paginate($page, $list = null)
    {
        if ($page instanceof LengthAwarePaginator) {
            $total = $page->total();
            return [
                'total' => $total,
                'page' => $total == 0 ? 0 : $page->currentPage(),
                'limit' => $page->perPage(),
                'pages' => $total == 0 ? 0 : $page->lastPage(),
                'list' => $list ?? $page->items()
            ];
        }

        if ($page instanceof Collection) {
            $page = $page->toArray();
        }

        if (!is_array($page)) {
            return $page;
        }

        return [
            'total' => $page['total'] ?? 0,
            'page' => $page['page'] ?? 0,
            'limit' => $page['limit'] ?? 0,
            'pages' => $page['total'] ?? 0,
            'list' => $list ?? $page['list'] ?? []
        ];
    }

    protected function failOrSuccess(bool $isSuccess, $codeResponse = CodeResponse::FAIL, $data = null, $info = '')
    {
        if ($isSuccess) {
            return $this->success($data);
        }
        return $this->fail($codeResponse, $info);
    }

    protected function success($data = null, $info = '')
    {
        return $this->codeReturn(CodeResponse::SUCCESS, $data, $info);
    }

    protected function badArgument()
    {
        return $this->fail(CodeResponse::PARAM_ILLEGAL);
    }

    protected function badArgumentValue()
    {
        return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL);
    }

    protected function fail(array $codeResponse, $info = '', $data = null)
    {
        return $this->codeReturn($codeResponse, $data, $info);
    }

    private function codeReturn(array $codeResponse, $data = null, $info = '')
    {
        list($statusCode, $code, $message) = $codeResponse;
        $res = ['code' => $code];
        if (is_array($data)) {
            $data = array_filter($data, function ($item) {
                return $item !== null;
            });
            $res['data'] = $data;
        } elseif (!is_null($data)) {
            $res['data'] = $data;
        }
        $res['message'] = $info ?: $message;
        return response()->json($res, $statusCode);
    }
}
