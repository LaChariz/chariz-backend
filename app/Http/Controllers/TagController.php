<?php

namespace App\Http\Controllers;
use App\Models\Tag;
use App\Http\Resources\TagResource;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::all();
        return TagResource::collection($tags);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255|unique:projects',
            ]);
            
            $tag = Tag::create($data);
                        
            return new TagResource($tag);

        } catch(\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($tagId)
    {
        $tag = Tag::findOrFail($tagId);
        return new TagResource($tag);
    }

    public function update(Request $request, $tagId)
    {
        try {
            $data = $request->validate([
                'name' => 'sometimes|string|max:255',
            ]);

            $tag = Tag::findOrFail($tagId);

            $tag->update($data);
    
            return new TagResource($tag->fresh());

        } catch(\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($tagId)
    {
        $tag = Tag::findOrFail($tagId);
        $tag->delete();
        return response()->json(['message' => 'Product tag deleted.'], 200);
    }
}