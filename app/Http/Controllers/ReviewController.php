<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReviewResource;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index()
    {
        return ReviewResource::collection(
            Review::with(['user', 'product'])->latest()->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'    => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'title'      => 'required|string',
            'rate'       => 'required|integer|min:1|max:5',
            'priority'   => 'nullable|integer',
            'status'     => 'in:Active,Inactive',
        ]);

        $review = Review::create($validated);
        return new ReviewResource($review->load(['user', 'product']));
    }

    public function show($id)
    {
        $review = Review::with(['user', 'product'])->findOrFail($id);
        return new ReviewResource($review);
    }

    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        $validated = $request->validate([
            'title'      => 'sometimes|string',
            'rate'       => 'sometimes|integer|min:1|max:5',
            'priority'   => 'nullable|integer',
            'status'     => 'in:Active,Inactive',
        ]);

        $review->update($validated);
        return new ReviewResource($review->load(['user', 'product']));
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();
        return response()->json(['message' => 'Review deleted successfully']);
    }


    public function addReview(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'title' => 'required|string|max:255',
            'rate' => 'required|integer|min:1|max:5',
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $review = Review::create([
            'product_id' => $request->product_id,
            'user_id' => $user->id,
            'title' => $request->title,
            'rate' => $request->rate,
        ]);

        return response()->json([
            'message' => 'Review submitted successfully',
            'review' => $review,
        ], 201);
    }
}
