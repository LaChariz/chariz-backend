<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return ProductResource::collection($products);
    }

    public function store(Request $request)
    {
        try {
            if (auth()->user()->role !== 'admin') {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
            $data = $request->validate([
                'product_name' => 'required|string|max:255|unique:products',
                'product_image' => 'required|url',
                'description' => 'nullable|string',
                'images' => 'nullable|array',
                'images.*' => 'url',
                'price' => 'required|integer',
                'quantity' => 'nullable|integer',
                'sold_items' => 'nullable|integer',
                'sales_price' => 'nullable|integer',
                'additional_info' => 'nullable|string',
                'sku' => 'nullable|string',
                'weight' => 'nullable|string',
                'dimensions' => 'nullable|string',
                'shipping_method' => 'nullable|string',
                'shipping_cost' => 'nullable|integer',
                'shipping_time' => 'nullable|string',
                'location' => 'nullable|string',
                'category_ids' => 'nullable|array',
                'category_ids.*' => 'exists:categories,id',
                'tag_ids' => 'nullable|array',
                'tag_ids.*' => 'exists:tags,id',
            ]);
            
            $data['images'] = json_encode($data['images']);
            $data['status'] = 'published';
            $data['visibility'] = true;

            $product = Product::create($data);

            if ($request->has('category_ids')) {
                $product->categories()->sync($request->category_ids);
            }

            if ($request->has('tag_ids')) {
                $product->tags()->sync($request->tag_ids);
            }
                        
            return new ProductResource($product);

        } catch(\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($productId)
    {
        $product = Product::findOrFail($productId);
        return new ProductResource($product);
    }

    public function update(Request $request, $productId)
    {
        try {
            $data = $request->validate([
                'product_name' => 'sometimes|string|max:255|unique:products',
                'product_image' => 'sometimes|url',
                'images' => 'sometimes|array',
                'images.*' => 'url',
                'price' => 'sometimes|integer',
                'quantity' => 'sometimes|integer',
                'sold_items' => 'sometimes|integer',
                'sales_price' => 'sometimes|integer',
                'additional_info' => 'sometimes|string',
                'sku' => 'sometimes|string',
                'weight' => 'sometimes|string',
                'dimensions' => 'sometimes|string',
                'shipping_method' => 'sometimes|string',
                'shipping_cost' => 'sometimes|integer',
                'shipping_time' => 'sometimes|string',
                'location' => 'sometimes|string',
                'category_ids' => 'sometimes|array',
                'category_ids.*' => 'exists:categories,id',
                'tag_ids' => 'sometimes|array',
                'tag_ids.*' => 'exists:tags,id',
            ]);

            if ($request->has('images')) {
                $data['images'] = json_encode($data['images']);
            }
            
            $product = Product::findOrFail($productId);

            $product->update($data);

            $categoryIds = $request->input('category_ids', []);
            $product->categories()->sync($categoryIds);

            $tagIds = $request->input('tag_ids', []);
            $product->tags()->sync($tagIds);
    
            return new ProductResource($product->fresh());

        } catch(\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($productId)
    {
        $product = Product::findOrFail($productId);
        $product->delete();
        return response()->json(['message' => 'Product deleted.'], 200);
    }
}
