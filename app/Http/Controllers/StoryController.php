<?php

namespace App\Http\Controllers;

use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class StoryController extends Controller
{
    public function index()
    {
        $stories = Story::with('user')
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('user_id');

        return view('stories.index', compact('stories'));
    }

    public function userStories($userId)
    {
        $stories = Story::with('user')
            ->where('user_id', $userId)
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'stories' => $stories
        ]);
    }

    public function show(Story $story)
    {
        if ($story->expires_at < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Story has expired'
            ], 404);
        }

        // Mark story as viewed if it's not the user's own story
        if ($story->user_id !== Auth::id()) {
            $story->markAsViewed();
        }

        return response()->json([
            'success' => true,
            'story' => $story->load('user')
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120', // 5MB max
            'caption' => 'nullable|string|max:255',
        ]);

        $imagePath = $request->file('image')->store('stories', 'public');

        $story = Story::create([
            'user_id' => Auth::id(),
            'image' => $imagePath,
            'caption' => $request->caption,
            'viewed' => false,
            'expires_at' => now()->addHours(24)
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Story created successfully',
                'story' => $story
            ]);
        }

        return redirect()->route('profile.show', Auth::id())->with('success', 'Story created successfully');
    }

    public function destroy(Story $story)
    {
        if ($story->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        try {
            Storage::disk('public')->delete($story->image);
            $story->delete();

            return response()->json([
                'success' => true,
                'message' => 'Story deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete story'
            ], 500);
        }
    }
}
