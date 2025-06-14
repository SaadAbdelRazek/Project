<?php

namespace App\Http\Controllers;

use App\Http\Resources\PackageResource;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Carbon;

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

    //----------------------------------------------------------------

    public function createPackageCheckoutSession(Request $request)
    {
        $user = Auth::user();

        $package = Package::findOrFail($request->package_id);

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'unit_amount' => intval($package->price * 100),
                    'product_data' => [
                        'name' => $package->name,
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => 'http://localhost:5173/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => 'http://localhost:5173/cancel',
            'metadata' => [
                'user_id' => $user->id,
                'package_id' => $package->id,
            ],
        ]);

        return response()->json(['url' => $session->url]);
    }
}
