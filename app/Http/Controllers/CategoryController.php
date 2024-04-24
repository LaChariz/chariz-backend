<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return CategoryResource::collection($categories);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255|unique:projects',
                'description' => 'nullable|string',
            ]);
            
            $category = Category::create($data);
                        
            return new CategoryResource($category);

        } catch(\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        return new CategoryResource($category);
    }

    public function update(Request $request, $categoryId)
    {
        try {
            $data = $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
            ]);

            $category = Category::findOrFail($categoryId);

            $category->update($data);
    
            return new CategoryResource($category->fresh());

        } catch(\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $category->delete();
        return response()->json(['message' => 'Product category deleted.'], 200);
    }
}
