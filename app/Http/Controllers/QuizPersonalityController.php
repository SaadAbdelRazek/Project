<?php

namespace App\Http\Controllers;

use App\Models\Personality;
use Illuminate\Http\Request;

class QuizPersonalityController extends Controller
{
    public function index()
    {
        $personalities = Personality::all();
        return response()->json($personalities);
    }

    // Method to create a new personality
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'characteristics' => 'required|string',
            'preferred_decor' => 'required|string',
        ]);

        $personality = Personality::create([
            'name' => $request->name,
            'characteristics' => $request->characteristics,
            'preferred_decor' => $request->preferred_decor,
        ]);

        return response()->json($personality, 201);
    }

    // Method to show a specific personality
    public function show($id)
    {
        $personality = Personality::findOrFail($id);
        return response()->json($personality);
    }

    // Method to update an existing personality
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'characteristics' => 'required|string',
            'preferred_decor' => 'required|string',
        ]);

        $personality = Personality::findOrFail($id);
        $personality->update([
            'name' => $request->name,
            'characteristics' => $request->characteristics,
            'preferred_decor' => $request->preferred_decor,
        ]);

        return response()->json($personality);
    }

    // Method to delete a personality
    public function destroy($id)
    {
        $personality = Personality::findOrFail($id);
        $personality->delete();

        return response()->json(['message' => 'Personality deleted successfully']);
    }
}
