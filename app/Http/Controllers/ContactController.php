<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        Log::info('Incoming contact request', [
            'user' => Auth::user(),
            'payload' => $request->all()
        ]);

        $user = Auth::user();

        $contact = Contact::create([
            'name' => $user->name ?? $request->name,
            'email' => $user->email ?? $request->email,
            'message' => $request->message,
        ]);

        Log::info('Contact created', ['contact' => $contact]);

        return response()->json(['message' => 'Message sent successfully.']);
    }



    public function index()
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        $contacts = Contact::latest()->get();

        return response()->json([
            'contacts' => $contacts
        ]);
    }
}
