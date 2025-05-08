<?php

namespace App\Http\Controllers;

use App\Http\Resources\SubcategoryResource;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubcategoryController extends Controller
{
    public function index()
    {
        return SubcategoryResource::collection(
            Subcategory::with('category')->latest()->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'image'       => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'priority'    => 'required|integer',
            'status'      => 'required|in:1,0',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('subcategories', 'public');
        }

        $subcategory = Subcategory::create($validated);

        return new SubcategoryResource($subcategory);
    }

    public function show($id)
    {
        $subcategory = Subcategory::with('category')->findOrFail($id);
        return new SubcategoryResource($subcategory);
    }


    public function update(Request $request, $id)
    {
        $subcategory = Subcategory::with('category')->findOrFail($id);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'image'       => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'priority'    => 'required|integer',
            'status'      => 'required|in:Active,Inactive',
        ]);

        if ($request->hasFile('image')) {
            if ($subcategory->image) {
                Storage::disk('public')->delete($subcategory->image);
            }
            $validated['image'] = $request->file('image')->store('subcategories', 'public');
        }

        $subcategory->update($validated);

        return new SubcategoryResource($subcategory);
    }

    public function destroy($id)
    {
        $subcategory = Subcategory::findOrFail($id);

        if ($subcategory->image) {
            Storage::disk('public')->delete($subcategory->image);
        }

        $subcategory->delete();

        return response()->json(['message' => 'Subcategory deleted successfully']);
    }
}
