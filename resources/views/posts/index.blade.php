@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        @foreach($posts as $post)
            <div class="col-md-8 mb-4">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        @if($post->user)
                            <img src="{{ $post->user->profile_image ? asset('storage/' . $post->user->profile_image) : 'https://via.placeholder.com/40' }}" class="rounded-circle me-3" style="width: 40px; height: 40px;">
                            <a href="{{ route('profile.show', $post->user->id) }}" class="text-dark text-decoration-none">
                                <strong>{{ $post->user->username }}</strong>
                            </a>
                        @else
                            <img src="https://via.placeholder.com/40" class="rounded-circle me-3" style="width: 40px; height: 40px;">
                            <span class="text-muted">Deleted User</span>
                        @endif
                    </div>
                    <img src="{{ asset('storage/' . $post->image) }}" class="card-img-top">
                    <div class="card-body">
                        <div class="d-flex mb-2">
                            <button type="button" 
                                    class="btn btn-link p-0 me-2 like-button" 
                                    data-post-id="{{ $post->id }}"
                                    data-liked="{{ $post->likes->where('user_id', auth()->id())->count() > 0 ? 'true' : 'false' }}">
                                <i class="{{ $post->likes->where('user_id', auth()->id())->count() > 0 ? 'fas' : 'far' }} fa-heart {{ $post->likes->where('user_id', auth()->id())->count() > 0 ? 'text-danger' : '' }}"></i>
                            </button>
                            <a href="{{ route('posts.show', $post->id) }}" class="btn btn-link p-0">
                                <i class="far fa-comment"></i>
                            </a>
                        </div>
                        <p><strong class="like-count-{{ $post->id }}">{{ $post->likes->count() }}</strong> likes</p>
                        @if($post->user)
                            <p><strong>{{ $post->user->username }}</strong> {{ $post->caption }}</p>
                        @else
                            <p>{{ $post->caption }}</p>
                        @endif
                        <a href="{{ route('posts.show', $post->id) }}" class="text-muted">
                            View all {{ $post->comments->count() }} comments
                        </a>
                        <p class="text-muted mt-1">{{ $post->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const likeButtons = document.querySelectorAll('.like-button');
    
    likeButtons.forEach(button => {
        button.addEventListener('click', function() {
            console.log('Like button clicked!', this);
            const postId = this.dataset.postId;
            let isLiked = this.dataset.liked === 'true';
            const icon = this.querySelector('i');
            const likeCount = document.querySelector(`.like-count-${postId}`);
            const btn = this;
            btn.disabled = true;
            const url = `/posts/${postId}/likes`;
            // Always use POST, send _method: DELETE for unliking
            const fetchOptions = {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: isLiked ? JSON.stringify({ _method: 'DELETE' }) : undefined
            };
            console.log('Sending', isLiked ? 'DELETE' : 'POST', 'to', url, fetchOptions);
            fetch(url, fetchOptions)
            .then(response => {
                console.log('AJAX response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('AJAX response data:', data);
                likeCount.textContent = data.likes_count;
                // Toggle like state
                isLiked = !isLiked;
                if (!isLiked) {
                    icon.classList.remove('fas', 'text-danger');
                    icon.classList.add('far');
                    btn.dataset.liked = 'false';
                    icon.outerHTML = '<i class="far fa-heart"></i>';
                    console.log('Set to unliked');
                } else {
                    icon.classList.remove('far');
                    icon.classList.add('fas', 'text-danger');
                    btn.dataset.liked = 'true';
                    icon.outerHTML = '<i class="fas fa-heart text-danger"></i>';
                    console.log('Set to liked');
                }
                btn.disabled = false;
            })
            .catch(error => {
                console.error('Error:', error);
                btn.disabled = false;
            });
        });
    });
});
</script>
@endpush
@endsection