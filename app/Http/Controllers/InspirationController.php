<?php

namespace App\Http\Controllers;
use App\Models\Inspiration;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\InspirationResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class InspirationController extends Controller
{
    public function index()
    {
        return InspirationResource::collection(Inspiration::latest()->get());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $path = $request->file('photo')->store('inspirations', 'public');

        $inspiration = Inspiration::create([
            'photo' => $path,
        ]);

        return new InspirationResource($inspiration);
    }

    public function destroy($id)
    {
        $inspiration = Inspiration::findOrFail($id);
        Storage::disk('public')->delete($inspiration->photo);
        $inspiration->delete();

        return response()->json(['message' => 'Inspiration deleted']);
    }
}
