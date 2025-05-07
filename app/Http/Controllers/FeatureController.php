<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FeatureController extends Controller
{
    public function showForm()
    {
        return view('generate-image');
    }

    public function generateImage(Request $request)
    {
        $prompt = $request->input('prompt');

        $response = Http::withOptions([
            'verify' => 'C:\xampp\php\extras\ssl\cacert.pem', // ← المسار الصحيح للـ PEM
        ])->withHeaders([
            'authorization' => 'Bearer sk-wfpZGZO0YUhGnD9v60lv2xm8EcTFChc7UAMdIkQAHcEhvjCB',
            'accept' => 'image/*',
        ])->asMultipart()->post('https://api.stability.ai/v2beta/stable-image/generate/core', [
            [
                'name' => 'prompt',
                'contents' => $prompt,
            ],
            [
                'name' => 'output_format',
                'contents' => 'webp',
            ],
        ]);

        if ($response->successful()) {
            $filename = 'generated_' . time() . '.webp';
            file_put_contents(public_path('images/' . $filename), $response->body());
            return view('generate-image', ['image' => asset('images/' . $filename)]);
        } else {
            return back()->with('error', 'فشل التوليد: ' . $response->body());
        }
    }

    //------------------------------------------------------------------



}
