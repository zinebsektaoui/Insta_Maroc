<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    public function follow(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot follow yourself.');
        }

        if (!Auth::user()->isFollowing($user)) {
            Auth::user()->following()->attach($user->id);
            return back()->with('success', 'You are now following ' . $user->username);
        }

        return back()->with('error', 'You are already following this user.');
    }

    public function unfollow(User $user)
    {
        if (Auth::user()->isFollowing($user)) {
            Auth::user()->following()->detach($user->id);
            return back()->with('success', 'You have unfollowed ' . $user->username);
        }

        return back()->with('error', 'You are not following this user.');
    }
}
