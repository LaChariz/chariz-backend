<?php

namespace App\Http\Controllers;

use App\Http\Resources\GalleryResource;
use App\Models\Gallery;
use App\Models\GalleryCategory;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index()
    {
        $galleries = Gallery::all();
        return GalleryResource::collection($galleries);
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        try {
            $request->validate([
                'image' => 'required|string',
                'gallery_category_id' => 'required|exists:gallery_categories,id',
            ]);
    
            $gallery_category = GalleryCategory::findOrFail($request->gallery_category_id);
    
            $gallery = new Gallery([
                'image' => $request->image,
                'status' => 'published',
                'visibility' => true,
            ]);
    
            $gallery_category->galleries()->save($gallery);

            return new GalleryResource($gallery);

        } catch(\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
        
    }

    public function show($galleryId)
    {
        $gallery = Gallery::with('category')->findOrFail($galleryId);
        return new GalleryResource($gallery);
    }

    public function update(Request $request, $galleryId)
    {
        try {
            $data = $request->validate([
                'image' => 'sometimes|string',
                'status' => 'sometimes|string|max:255',
                'visibility' => 'sometimes|string|max:255',
                'gallery_category_id' => 'sometimes|exists:gallery_categories,id',
            ]);

            $gallery = Gallery::findOrFail($galleryId);
    
            if ($request->filled('gallery_category_id')) {
                $galleryCategory = GalleryCategory::find($data['gallery_category_id']);
    
                if (!$galleryCategory) {
                    return response()->json(['error' => 'Category not found'], 404);
                }
            }
    
            $gallery->update($data);
    
            return new GalleryResource($gallery->fresh());

        } catch(\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($galleryId)
    {
        $gallery = Gallery::findOrFail($galleryId);
        $gallery->delete();
        return response()->json(['message' => 'Gallery image deleted.'], 200);
    }
}
