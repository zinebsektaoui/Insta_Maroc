@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Your Conversations</h5>
                </div>
                <div class="card-body p-0">
                    @if($conversations->isEmpty())
                        <div class="p-4 text-center text-muted">No conversations yet.</div>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($conversations as $otherUserId => $messages)
                                @if($otherUserId == auth()->id())
                                    @continue
                                @endif
                                @php
                                    $lastMessage = $messages->last();
                                    $otherUser = $lastMessage->sender_id == auth()->id() ? $lastMessage->receiver : $lastMessage->sender;
                                @endphp
                                <a href="/messages/{{ $otherUser->id }}" class="text-decoration-none conversation-link">
                                    <li class="list-group-item d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $otherUser->profile_image ? asset('storage/' . $otherUser->profile_image) : 'https://via.placeholder.com/48' }}" class="rounded-circle me-3" style="width: 48px; height: 48px; object-fit: cover;">
                                            <div>
                                                <div class="fw-bold">{{ $otherUser->username }}</div>
                                                <div class="text-muted" style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                    {{ $lastMessage->content }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-end text-muted" style="font-size: 0.9em; min-width: 80px;">
                                            {{ $lastMessage->created_at->diffForHumans() }}
                                        </div>
                                    </li>
                                </a>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.conversation-link:hover {
    background: #f8f9fa;
}
[data-bs-theme="dark"] .conversation-link:hover {
    background: #23272b;
}
</style>
@endsection 