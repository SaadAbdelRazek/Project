<?php

namespace App\Http\Controllers;

use App\Http\Resources\SaleResource;
use App\Models\Sale;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index()
    {
        return SaleResource::collection(Sale::with('product')->latest()->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'percentage' => 'required|numeric|min:0|max:100',
        ]);

        $sale = Sale::create($validated);

        return new SaleResource($sale->load('product'));
    }

    public function show($id)
    {
        $sale = Sale::with('product')->findOrFail($id);
        return new SaleResource($sale);
    }

    public function update(Request $request, $id)
    {
        $sale = Sale::findOrFail($id);

        $validated = $request->validate([
            'product_id' => 'sometimes|exists:products,id',
            'percentage' => 'sometimes|numeric|min:0|max:100',
        ]);

        $sale->update($validated);

        return new SaleResource($sale->load('product'));
    }

    public function destroy($id)
    {
        $sale = Sale::findOrFail($id);
        $sale->delete();

        return response()->json(['message' => 'Sale deleted successfully']);
    }
}
