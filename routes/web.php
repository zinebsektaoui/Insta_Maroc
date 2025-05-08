<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\StoryController;

// Set homepage to show posts feed (requires authentication)
Route::get('/', [PostController::class, 'index'])
    ->middleware('auth')
    ->name('home');

Route::get('/home', function () {
    return redirect('/'); // Redirect to your main feed
})->name('home');

Auth::routes();

// Post routes
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
Route::patch('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

// Comment routes
Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

// Like routes
Route::post('/posts/{post}/likes', [LikeController::class, 'store'])->name('likes.store');
Route::delete('/posts/{post}/likes', [LikeController::class, 'destroy'])->name('likes.destroy');

Route::middleware(['auth'])->group(function () {
    // Profile routes
    Route::get('/profile/{user}', [ProfileController::class, 'index'])->name('profile.show');
    Route::get('/profile/{user}/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/{user}', [ProfileController::class, 'update'])->name('profile.update');

    // Follow routes
    Route::post('/users/{user}/follow', [FollowController::class, 'follow'])->name('follow');
    Route::post('/users/{user}/unfollow', [FollowController::class, 'unfollow'])->name('unfollow');
    
    // Messaging routes
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{user}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{user}', [MessageController::class, 'store'])->name('messages.store');

    // Story routes
    Route::get('/stories/user/{user}', [StoryController::class, 'userStories'])->name('stories.user');
    Route::post('/stories', [StoryController::class, 'store'])->name('stories.store');
    Route::delete('/stories/{story}', [StoryController::class, 'destroy'])->name('stories.destroy');
});