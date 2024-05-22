<?php

namespace App\Http\Controllers;

use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::all();
        return ServiceResource::collection($services);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255|unique:services',
                'images' => 'nullable|array',
                'images.*' => 'url',
                'description' => 'nullable|string',
            ]);
            
            $data['images'] = json_encode($data['images']);
            $data['status'] = 'published';

            $service = Service::create($data);
                        
            return new ServiceResource($service);

        } catch(\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($serviceId)
    {
        $service = Service::findOrFail($serviceId);
        return new ServiceResource($service);
    }

    public function update(Request $request, $serviceId)
    {
        try {
            $data = $request->validate([
                'name' => [
                    'sometimes',
                    'string',
                    'max:255',
                    Rule::unique('services')->ignore($serviceId)
                ],
                'images' => 'sometimes|array',
                'description' => 'sometimes|string',
                'status' => 'sometimes|in:published,draft',
            ]);

            $service = Service::findOrFail($serviceId);

            $service->update($data);
    
            return new ServiceResource($service->fresh());

        } catch(\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($serviceId)
    {
        $service = Service::findOrFail($serviceId);
        $service->delete();
        return response()->json(['message' => 'Service deleted.'], 200);
    }
}
