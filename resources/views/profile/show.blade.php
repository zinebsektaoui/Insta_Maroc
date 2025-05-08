@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Debug Information -->
    <div class="alert alert-info">
        Debug Info:<br>
        Auth Check: {{ Auth::check() ? 'Yes' : 'No' }}<br>
        Auth ID: {{ Auth::id() }}<br>
        Profile User ID: {{ $user->id }}<br>
        Is Same User: {{ Auth::id() === $user->id ? 'Yes' : 'No' }}
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card border-0">
                <div class="card-body p-0">
                    <div class="d-flex align-items-center mb-4">
                        <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('images/default-profile.png') }}" 
                             alt="{{ $user->username }}" 
                             class="rounded-circle me-4"
                             style="width: 150px; height: 150px; object-fit: cover;">
                        
                        <div class="profile-info">
                            <h2 class="h4 mb-3">{{ $user->username }}</h2>
                            
                            @if(Auth::check() && Auth::id() !== $user->id)
                                <div class="d-flex gap-2">
                                    @if(Auth::user()->isFollowing($user))
                                        <form action="{{ route('unfollow', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-secondary btn-sm px-4">Following</button>
                                        </form>
                                    @else
                                        <form action="{{ route('follow', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-sm px-4">Follow</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('messages.index', $user->id) }}" class="btn btn-outline-primary btn-sm px-4">
                                        <i class="fas fa-envelope"></i> Message
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex mb-4">
                        <div class="me-4">
                            <strong>{{ $user->posts->count() }}</strong> posts
                        </div>
                        <div class="me-4">
                            <strong>{{ $user->followers->count() }}</strong> followers
                        </div>
                        <div>
                            <strong>{{ $user->following->count() }}</strong> following
                        </div>
                    </div>

                    <div class="user-info">
                        <h5 class="mb-1">{{ $user->name }}</h5>
                        @if($user->bio)
                            <p class="text-muted mb-0">{{ $user->bio }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="row g-3">
                @foreach($user->posts as $post)
                    <div class="col-md-4">
                        <div class="card border-0">
                            <a href="{{ route('posts.show', $post) }}" class="text-decoration-none">
                                <img src="{{ asset('storage/' . $post->image) }}" 
                                     class="card-img-top" 
                                     alt="{{ $post->caption }}"
                                     style="aspect-ratio: 1; object-fit: cover;">
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
.profile-info {
    flex: 1;
}

.btn-outline-secondary {
    border-color: #dbdbdb;
    color: #262626;
}

.btn-outline-secondary:hover {
    background-color: #dbdbdb;
    color: #262626;
}

.btn-primary {
    background-color: #0095f6;
    border-color: #0095f6;
}

.btn-primary:hover {
    background-color: #0086e6;
    border-color: #0086e6;
}

.card {
    box-shadow: none;
}

.user-info {
    margin-top: 1rem;
}
</style>
@endsection 