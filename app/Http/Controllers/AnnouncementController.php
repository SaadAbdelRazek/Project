<?php

namespace App\Http\Controllers;

use App\Http\Resources\AnnouncementResource;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    public function index()
    {
        return AnnouncementResource::collection(Announcement::latest()->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'image'      => 'nullable|image',
            'percentage' => 'required|integer|min:0|max:100',
            'status'      => 'required|in:1,0',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('announcements', 'public');
        }

        $announcement = Announcement::create($data);

        return new AnnouncementResource($announcement);
    }

    public function show($id)
    {
        return new AnnouncementResource(Announcement::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);

        $data = $request->validate([
            'title'      => 'sometimes|string|max:255',
            'image'      => 'nullable|image',
            'percentage' => 'sometimes|integer|min:0|max:100',
            'status'     => 'sometimes|in:enabled,disabled',
        ]);

        if ($request->hasFile('image')) {
            if ($announcement->image) {
                Storage::disk('public')->delete($announcement->image);
            }
            $data['image'] = $request->file('image')->store('announcements', 'public');
        }

        $announcement->update($data);

        return new AnnouncementResource($announcement);
    }

    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        if ($announcement->image) {
            Storage::disk('public')->delete($announcement->image);
        }
        $announcement->delete();

        return response()->json(['message' => 'Announcement deleted successfully']);
    }
}
