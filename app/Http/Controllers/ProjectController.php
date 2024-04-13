<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::all();
        return ProjectResource::collection($projects);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255|unique:projects',
                'images' => 'required|array',
                'images.*' => 'url',
                'description' => 'nullable|string',
                'architect' => 'nullable|string|max:255',
                'designer' => 'nullable|string|max:255',
                'concept' => 'nullable|string',
                'location' => 'nullable|string',
                'date' => 'nullable|date',
                'link' => 'nullable',
            ]);
            
            $data['images'] = json_encode($data['images']);
            $data['status'] = 'published';
            $data['visibility'] = true;

            $project = Project::create($data);
                        
            return new ProjectResource($project);

        } catch(\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($projectId)
    {
        $project = Project::findOrFail($projectId);
        return new ProjectResource($project);
    }

    public function update(Request $request, $projectId)
    {
        try {
            $data = $request->validate([
                'name' => 'sometimes|string|max:255',
                'images' => 'sometimes|array',
                'description' => 'sometimes|string',
                'architect' => 'sometimes|string|max:255',
                'designer' => 'sometimes|string|max:255',
                'concept' => 'sometimes|string',
                'location' => 'sometimes|string',
                'date' => 'sometimes|date',
                'link' => 'sometimes',
                'status' => 'sometimes|in:published,draft',
                'visibility' => 'sometimes|boolean'
            ]);

            $project = Project::findOrFail($projectId);

            $project->update($data);
    
            return new ProjectResource($project->fresh());

        } catch(\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($projectId)
    {
        $project = Project::findOrFail($projectId);
        $project->delete();
        return response()->json(['message' => 'Project deleted.'], 200);
    }
}
