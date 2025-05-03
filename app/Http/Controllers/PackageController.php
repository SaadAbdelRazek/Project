<?php

namespace App\Http\Controllers;

use App\Http\Resources\PackageResource;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PackageController extends Controller
{
    public function index()
    {
        return PackageResource::collection(Package::latest()->get());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'  => 'required|string|max:255',
            'items' => 'required|string', // You can make it array and use json_encode if needed
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $package = Package::create($request->only('name', 'items', 'price'));

        return new PackageResource($package);
    }

    public function show($id)
    {
        $package = Package::findOrFail($id);
        return new PackageResource($package);
    }

    public function update(Request $request, $id)
    {
        $package = Package::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'  => 'sometimes|string|max:255',
            'items' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $package->update($request->only('name', 'items', 'price'));

        return new PackageResource($package);
    }

    public function destroy($id)
    {
        $package = Package::findOrFail($id);
        $package->delete();

        return response()->json(['message' => 'Package deleted successfully']);
    }
}
