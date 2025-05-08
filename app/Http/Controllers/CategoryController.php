<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('priority')->get();
        return CategoryResource::collection($categories);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'priority' => 'nullable|integer',
            'status' => 'required',
        ]);

        $data['status'] = filter_var($data['status'], FILTER_VALIDATE_BOOLEAN);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category = Category::create($data);

        return response()->json([
            'message' => 'Category created successfully',
            'category' => $category,
        ], 201);
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);

        if ($category->image) {
            $category->image = asset('storage/' . $category->image);
        }

        return response()->json($category);
    }


    public function update(Request $request, $id)
    {
        // First, log the raw request data
        Log::info('Raw request data:', $request->all());
        Log::info('Request headers:', $request->headers->all());

        $category = Category::findOrFail($id);

        // Log files separately
        Log::info('Has file image:', ['hasFile' => $request->hasFile('image')]);

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'priority' => 'nullable|integer',
            'status' => 'nullable|string|in:0,1',
        ]);

        $data = [];

        if ($request->has('name')) {
            $data['name'] = $request->input('name');
        }

        if ($request->has('description')) {
            $data['description'] = $request->input('description');
        }

        if ($request->has('priority')) {
            $data['priority'] = $request->input('priority');
        }

        if ($request->has('status')) {
            $data['status'] = $request->input('status');
        }

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        return response()->json($category);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted']);
    }


}
