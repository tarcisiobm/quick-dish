<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends ApiController
{
    protected string $model = User::class;
    protected string $name = 'User';
    protected array $with = ['employee'];

    public function index(Request $request): JsonResponse
    {
        $query = $this->model::query()->with($this->with);

        if ($request->has('is_employee')) {
            $isEmployee = filter_var($request->input('is_employee'), FILTER_VALIDATE_BOOLEAN);
            $isEmployee ? $query->whereHas('employee') : $query->whereDoesntHave('employee');
        }

        return $this->successResponse(null, $query->get());
    }
}
