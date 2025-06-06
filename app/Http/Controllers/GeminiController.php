<?php

namespace App\Http\Controllers;

use GeminiAPI\Laravel\Facades\Gemini;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GeminiController extends Controller
{
    public function sendMessage(Request $request)
    {
        // Step 1: Validate the incoming request to ensure the message is present and is a string
        $request->validate([
            'message' => 'required|string',
        ]);
        $response = Gemini::generateText($request->message);

        return response()->json([
            'response' => $response
        ]);

    }
}
