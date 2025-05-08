@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : 'https://via.placeholder.com/40' }}" 
                         class="rounded-circle me-3" 
                         style="width: 40px; height: 40px; object-fit: cover;">
                    <h5 class="mb-0">{{ $user->username }}</h5>
                </div>
                <div class="card-body">
                    <div class="messages-container" id="messagesContainer" style="height: 400px; overflow-y: auto;">
                        @foreach($messages as $message)
                            <div class="message {{ $message->sender_id === auth()->id() ? 'sent' : 'received' }} mb-3">
                                <div class="message-content p-3 rounded {{ $message->sender_id === auth()->id() ? 'bg-primary text-white' : 'bg-light' }}" 
                                     style="max-width: 70%; {{ $message->sender_id === auth()->id() ? 'margin-left: auto;' : 'margin-right: auto;' }}">
                                    {{ $message->content }}
                                    <div class="message-time text-end" style="font-size: 0.8em; opacity: 0.7;">
                                        {{ $message->created_at->format('g:i A') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <form id="messageForm" class="mt-3">
                        @csrf
                        <div class="input-group">
                            <input type="text" 
                                   name="content" 
                                   class="form-control" 
                                   placeholder="Type a message..." 
                                   required>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Send
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageForm = document.getElementById('messageForm');
    const messagesContainer = document.getElementById('messagesContainer');
    const messageInput = messageForm.querySelector('input[name="content"]');

    // Scroll to bottom on load
    messagesContainer.scrollTop = messagesContainer.scrollHeight;

    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const content = messageInput.value.trim();
        if (!content) return;

        // Optimistically add message to chat
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', { 
            hour: 'numeric', 
            minute: 'numeric', 
            hour12: true 
        });
        const messageHtml = `
            <div class="message sent mb-3">
                <div class="message-content p-3 rounded bg-primary text-white" style="max-width: 70%; margin-left: auto;">
                    ${content}
                    <div class="message-time text-end" style="font-size: 0.8em; opacity: 0.7;">
                        ${timeString}
                    </div>
                </div>
            </div>
        `;
        messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
        messageInput.value = '';
        messageInput.focus();

        // Send message to server
        fetch(`{{ route('messages.store', $user->id) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ content: content })
        }).catch(error => console.error('Error:', error));
    });
});
</script>
@endpush

<style>
.messages-container {
    scrollbar-width: thin;
    scrollbar-color: rgba(0, 0, 0, 0.2) transparent;
}

.messages-container::-webkit-scrollbar {
    width: 6px;
}

.messages-container::-webkit-scrollbar-track {
    background: transparent;
}

.messages-container::-webkit-scrollbar-thumb {
    background-color: rgba(0, 0, 0, 0.2);
    border-radius: 3px;
}

[data-bs-theme="dark"] .message-content.bg-light {
    background-color: #2d2d2d !important;
    color: #ffffff !important;
}

[data-bs-theme="dark"] .message-time {
    color: #a0a0a0 !important;
}

.btn-primary:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.input-group input:disabled {
    background-color: #f8f9fa;
    cursor: not-allowed;
}
</style>
@endsection 