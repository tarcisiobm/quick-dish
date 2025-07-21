<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\ReviewRequest;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends ApiController
{
    protected string $model = Review::class;
    protected string $name = 'Review';
    protected ?string $formRequest = ReviewRequest::class;
    protected array $with = ['user'];

    public function index(Request $request): JsonResponse
    {
        $query = $this->model::query()->where('status', true)->with($this->with);
        return $this->successResponse(null, $query->latest()->get());
    }

    public function store(Request $request): JsonResponse
    {
        $formRequest = app($this->formRequest);
        $validatedData = $formRequest->validated();

        $validatedData['user_id'] = $request->user()->id;

        $reviewExists = $this->model::where('user_id', $validatedData['user_id'])->exists();
        if ($reviewExists) {
            return $this->errorResponse('You have already submitted a review.', null, 409);
        }

        $resource = $this->model::create($validatedData);

        return $this->successResponse('created', $this->loadWithRelations($resource));
    }
}
