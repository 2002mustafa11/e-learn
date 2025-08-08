<?php

namespace App\Http\Controllers\Api\Category;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\ApiResponse;
use App\Http\Requests\CategoryRequest;

class CategoryController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $categories = Category::all();
        return $this->successResponse($categories, 'successful');
    }

    public function store(CategoryRequest $request)
    {
        $category = Category::create($request->validated());
        return $this->successResponse($category, 'successful', 201);
    }

    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return $this->errorResponse('الفئة غير موجودة', [], 404);
        }

        return $this->successResponse($category, 'successful');
    }

    public function update(CategoryRequest $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return $this->errorResponse('Category not found', [], 404);
        }

        $category->update($request->validated());
        return $this->successResponse($category, 'successful');
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return $this->errorResponse('Category not found', [], 404);
        }

        $category->delete();
        return $this->successResponse([], 'successful');
    }
}
