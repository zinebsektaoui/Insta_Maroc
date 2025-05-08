<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class LikeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Post $post)
    {
        // Check if user already liked the post
        if (!$post->likes()->where('user_id', auth()->id())->exists()) {
            $post->likes()->create([
                'user_id' => auth()->id(),
            ]);
        }

        if (request()->ajax()) {
            return response()->json([
                'likes_count' => $post->likes()->count()
            ]);
        }

        return back();
    }

    public function destroy(Post $post)
    {
        // Remove the like
        $post->likes()->where('user_id', auth()->id())->delete();

        if (request()->ajax()) {
            return response()->json([
                'likes_count' => $post->likes()->count()
            ]);
        }

        return back();
    }
}