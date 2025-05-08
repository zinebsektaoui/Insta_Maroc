@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Profile Content -->
    <div class="row">
        <div class="col-md-3 text-center">
            <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : 'https://via.placeholder.com/150' }}" class="rounded-circle w-100" style="max-width: 150px;">
            <!-- Stories Bar -->
            <div class="stories-bar mt-4 mb-4">
                <div class="d-flex align-items-center gap-3 overflow-auto pb-2 scrollbar-hide">
                    <!-- Add Story Button (only for profile owner) -->
                    @if(auth()->id() === $user->id)
                    <div class="flex-shrink-0 flex flex-col align-items-center">
                        <button type="button" class="btn p-0 border-0 bg-transparent" onclick="openAddStoryModal()">
                            <div class="position-relative" style="width:64px;height:64px;">
                                <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : 'https://via.placeholder.com/150' }}" class="rounded-circle border border-2 border-primary w-100 h-100 object-cover">
                            </div>
                        </button>
                        <span class="text-xs mt-1 text-center">Add Story</span>
                    </div>
                    @endif
                    <!-- User's Stories (one circle, all stories grouped) -->
                    @if($user->activeStories->count())
                    <div class="flex-shrink-0 flex flex-col align-items-center">
                        <button type="button" class="btn p-0 border-0 bg-transparent" onclick="openStoriesModal()">
                            <div class="position-relative" style="width:64px;height:64px;">
                                @php
                                    $count = $user->activeStories->count();
                                    $radius = 28; // slightly smaller so the ring is outside the image
                                    $circumference = 2 * 3.1416 * $radius;
                                    $segmentLength = $circumference / $count;
                                    $gap = 3; // px gap between segments
                                @endphp
                                <svg width="64" height="64" style="position:absolute;top:0;left:0;z-index:1;pointer-events:none;" viewBox="0 0 64 64">
                                    @for($i = 0; $i < $count; $i++)
                                        <circle
                                            r="28"
                                            cx="32"
                                            cy="32"
                                            fill="none"
                                            stroke="{{ $i % 2 == 0 ? '#e3342f' : '#38c172' }}"
                                            stroke-width="6"
                                            stroke-dasharray="{{ $segmentLength - $gap }},{{ $circumference - ($segmentLength - $gap) }}"
                                            stroke-dashoffset="-{{ $i * $segmentLength }}"
                                            />
                                    @endfor
                                </svg>
                                <img src="{{ asset('storage/' . $user->activeStories->first()->image) }}" class="rounded-circle w-100 h-100 object-cover" style="position:relative;z-index:2;">
                            </div>
                        </button>
                        <span class="text-xs mt-1 text-center">{{ $user->username }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="d-flex align-items-center">
                <h1>{{ $user->username }}</h1>
                @if(auth()->check() && auth()->id() !== $user->id)
                    <div class="d-flex gap-2 ms-4">
                        @if(auth()->user()->isFollowing($user))
                            <form action="{{ route('unfollow', $user) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary">Following</button>
                            </form>
                        @else
                            <form action="{{ route('follow', $user) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-primary">Follow</button>
                            </form>
                        @endif
                        <a href="{{ route('messages.index', $user->id) }}" class="btn btn-outline-primary">
                            <i class="fas fa-envelope"></i> Message
                        </a>
                    </div>
                @elseif(auth()->id() === $user->id)
                    <a href="{{ route('profile.edit', $user->id) }}" class="btn btn-outline-secondary ms-4 edit-profile-btn">
                        <i class="fas fa-edit"></i> Edit Profile
                    </a>
                @endif
            </div>
            <div class="d-flex mt-3">
                <div class="me-4"><strong>{{ $user->posts->count() }}</strong> posts</div>
                <div class="me-4"><strong>{{ $user->followers->count() }}</strong> followers</div>
                <div><strong>{{ $user->following->count() }}</strong> following</div>
            </div>
            <div class="mt-3">
                <h5>{{ $user->name }}</h5>
                <p>{{ $user->bio }}</p>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        @foreach($user->posts as $post)
            <div class="col-md-4 mb-4">
                <a href="{{ route('posts.show', $post->id) }}">
                  <img src="{{ asset('storage/' . $post->image) }}" class="w-100">
                </a>
            </div>
        @endforeach
    </div>
</div>

<!-- Add Story Modal -->
<div class="modal fade" id="addStoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Story</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="storyForm" action="{{ route('stories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="storyImage" class="form-label">Image</label>
                        <input type="file" class="form-control" id="storyImage" name="image" required accept="image/*">
                        <div id="imagePreview" class="mt-2" style="display: none;">
                            <img src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="caption" class="form-label">Caption (optional)</label>
                        <textarea class="form-control" id="caption" name="caption" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Story</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Stories Modal (Carousel) -->
<div class="modal fade" id="storiesModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div id="storiesCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="10000">
                    <div class="carousel-inner">
                        @foreach($user->activeStories as $index => $story)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <div class="story-progress-bar-container">
                                <div class="story-progress-bar"></div>
                            </div>
                            <img src="{{ asset('storage/' . $story->image) }}" class="d-block w-100" style="max-height: 80vh; object-fit: contain;">
                            @if($story->caption)
                            <div class="story-caption p-3">
                                <p class="mb-0">{{ $story->caption }}</p>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#storiesCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#storiesCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stories-container {
    background: #fff;
    border-radius: 8px;
    padding: 16px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    max-width: 100%;
    overflow-x: auto;
}

.story-item {
    width: 80px;
    flex-shrink: 0;
    cursor: pointer;
    transition: transform 0.2s;
}

.story-item:hover {
    transform: scale(1.05);
}

.story-avatar {
    position: relative;
    width: 60px;
    height: 60px;
    padding: 2px;
    border-radius: 50%;
    background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
    transition: all 0.3s ease;
}

.story-avatar:hover {
    transform: scale(1.1);
}

.story-avatar img {
    border: 2px solid #fff;
    transition: all 0.3s ease;
}

.story-avatar.has-unseen {
    background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
}

.story-avatar:not(.has-unseen) {
    background: #dbdbdb;
}

.add-story-icon {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 20px;
    height: 20px;
    background: #0095f6;
    border: 2px solid #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 12px;
    transition: all 0.3s ease;
}

.add-story-icon:hover {
    background: #0086e6;
    transform: scale(1.1);
}

.story-username {
    color: #262626;
    font-size: 12px;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.story-caption {
    background: rgba(0, 0, 0, 0.5);
    color: #fff;
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 15px;
    backdrop-filter: blur(5px);
}

.story-music {
    position: absolute;
    top: 15px;
    left: 15px;
    background: rgba(0, 0, 0, 0.5);
    color: #fff;
    padding: 8px 15px;
    border-radius: 20px;
    backdrop-filter: blur(5px);
    display: flex;
    align-items: center;
    gap: 8px;
}

.story-music i {
    font-size: 14px;
}

body[data-bs-theme="dark"] .stories-container {
    background: #18191a !important;
    color: #e4e6eb !important;
    box-shadow: 0 1px 2px rgba(0,0,0,0.4);
    border: 1px solid #333;
}

body[data-bs-theme="dark"] .story-username {
    color: #e4e6eb;
}

body[data-bs-theme="dark"] .modal-content {
    background: #1a1a1a;
    color: #e4e6eb;
}

body[data-bs-theme="dark"] .modal-header {
    border-bottom-color: #2d2d2d;
}

body[data-bs-theme="dark"] .modal-footer {
    border-top-color: #2d2d2d;
}

.btn-primary {
    background-color: #0095f6;
    border-color: #0095f6;
}

.btn-primary:hover {
    background-color: #0086e6;
    border-color: #0086e6;
}

.btn-outline-secondary {
    border-color: #dbdbdb;
    color: #262626;
}

.btn-outline-secondary:hover {
    background-color: #dbdbdb;
    color: #262626;
}

body[data-bs-theme="dark"] .btn-outline-secondary {
    border-color: #6c757d;
    color: #e4e6eb;
}

body[data-bs-theme="dark"] .btn-outline-secondary:hover {
    background-color: #6c757d;
    color: #fff;
}

#musicResults {
    max-height: 200px;
    overflow-y: auto;
}

#musicResults .list-group-item {
    cursor: pointer;
    transition: background-color 0.2s;
}

#musicResults .list-group-item:hover {
    background-color: #f8f9fa;
}

body[data-bs-theme="dark"] #musicResults .list-group-item:hover {
    background-color: #2d2d2d;
}

.edit-profile-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.edit-profile-btn:hover {
    transform: translateY(-1px);
}

body[data-bs-theme="dark"] .edit-profile-btn {
    color: #e4e6eb;
}

body[data-bs-theme="dark"] .edit-profile-btn:hover {
    color: #fff;
}

.stories-bar {
    background: #fff;
    border-radius: 8px;
    padding: 12px 8px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.06);
    max-width: 100%;
    overflow-x: auto;
}
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

#storiesModal .modal-content {
    border: 4px solid transparent;
    border-radius: 16px;
    position: relative;
    z-index: 1;
    overflow: visible;
    padding: 16px;
}
#storiesModal .modal-body {
    padding: 0;
}
#storiesModal .modal-content::before {
    content: "";
    position: absolute;
    inset: -4px;
    border-radius: 20px;
    z-index: -1;
    background: linear-gradient(270deg, #38c172, #e3342f, #38c172, #e3342f);
    background-size: 400% 400%;
    animation: borderMove 3s linear infinite;
}
@keyframes borderMove {
    0% { background-position: 0% 50%; }
    100% { background-position: 100% 50%; }
}
#storiesModal.show ~ .modal-backdrop {
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    background-color: rgba(0,0,0,0.2) !important;
}

.modal-backdrop.stories-blur {
    backdrop-filter: blur(50px);
    -webkit-backdrop-filter: blur(50px);
    background-color: rgba(0,0,0,0.25) !important;
}

#storiesModal .carousel-item img {
    border: 4px solid #111;
    border-radius: 12px;
    background: #000;
    box-sizing: border-box;
}

.story-progress-bar-container {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 6px;
    background: rgba(0,0,0,0.08);
    z-index: 10;
    border-radius: 8px 8px 0 0;
    overflow: hidden;
}
.story-progress-bar {
    height: 100%;
    width: 0%;
    background: #a259f7;
    transition: width 0s, opacity 0.1s;
    opacity: 1;
}

body[data-bs-theme="dark"] .stories-container,
body[data-bs-theme="dark"] .stories-container * {
    color: #e4e6eb !important;
}
</style>

@push('scripts')
<script>
function openAddStoryModal() {
    new bootstrap.Modal(document.getElementById('addStoryModal')).show();
}
function openStoriesModal() {
    const modal = new bootstrap.Modal(document.getElementById('storiesModal'));
    modal.show();
    // Ensure no auto-advance, only manual navigation
    const carousel = document.getElementById('storiesCarousel');
    if (carousel) {
        // Destroy any previous instance
        if (carousel.bsCarousel) {
            carousel.bsCarousel.dispose();
        }
        const bsCarousel = new bootstrap.Carousel(carousel, { interval: false, ride: false, pause: true });
        carousel.bsCarousel = bsCarousel;
    }
}
// Image preview
const imageInput = document.getElementById('storyImage');
const imagePreview = document.getElementById('imagePreview');
const previewImg = imagePreview ? imagePreview.querySelector('img') : null;
if(imageInput && previewImg) {
    imageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    });
}
// Update stories bar and modal in real time
function updateStoriesBar(stories) {
    // Find the stories bar container
    const storiesBar = document.querySelector('.stories-bar .d-flex');
    if (!storiesBar) return;

    // Find or create the stories container
    let storiesContainer = storiesBar.querySelector('.flex-shrink-0.flex.flex-col.align-items-center:nth-child(2)');
    if (!storiesContainer) {
        storiesContainer = document.createElement('div');
        storiesContainer.className = 'flex-shrink-0 flex flex-col align-items-center';
        storiesBar.appendChild(storiesContainer);
    }

    // Create button for opening stories modal
    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'btn p-0 border-0 bg-transparent';
    button.onclick = openStoriesModal;

    // Create story icon container
    const storyIconDiv = document.createElement('div');
    storyIconDiv.className = 'position-relative';
    storyIconDiv.style.width = '64px';
    storyIconDiv.style.height = '64px';

    // Create SVG ring
    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.setAttribute('width', '64');
    svg.setAttribute('height', '64');
    svg.setAttribute('style', 'position:absolute;top:0;left:0;z-index:1;pointer-events:none;');
    svg.setAttribute('viewBox', '0 0 64 64');

    const count = stories.length;
    const radius = 28;
    const circumference = 2 * Math.PI * radius;
    const segmentLength = circumference / count;
    const gap = 3;

    for (let i = 0; i < count; i++) {
        const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        circle.setAttribute('r', '28');
        circle.setAttribute('cx', '32');
        circle.setAttribute('cy', '32');
        circle.setAttribute('fill', 'none');
        circle.setAttribute('stroke', i % 2 === 0 ? '#e3342f' : '#38c172');
        circle.setAttribute('stroke-width', '6');
        circle.setAttribute('stroke-dasharray', `${segmentLength - gap},${circumference - (segmentLength - gap)}`);
        circle.setAttribute('stroke-dashoffset', `-${i * segmentLength}`);
        svg.appendChild(circle);
    }

    storyIconDiv.appendChild(svg);

    // Add story image
    if (stories.length > 0) {
        const img = document.createElement('img');
        img.src = '/storage/' + stories[0].image;
        img.className = 'rounded-circle w-100 h-100 object-cover';
        img.style.position = 'relative';
        img.style.zIndex = '2';
        storyIconDiv.appendChild(img);
    }

    button.appendChild(storyIconDiv);
    storiesContainer.innerHTML = '';
    storiesContainer.appendChild(button);

    // Update stories modal content
    const carouselInner = document.querySelector('#storiesCarousel .carousel-inner');
    if (carouselInner) {
        carouselInner.innerHTML = '';
        stories.forEach((story, index) => {
            const item = document.createElement('div');
            item.className = `carousel-item ${index === 0 ? 'active' : ''}`;
            
            const img = document.createElement('img');
            img.src = '/storage/' + story.image;
            img.className = 'd-block w-100';
            img.style.maxHeight = '80vh';
            img.style.objectFit = 'contain';
            
            item.appendChild(img);
            
            if (story.caption) {
                const caption = document.createElement('div');
                caption.className = 'story-caption p-3';
                caption.innerHTML = `<p class="mb-0">${story.caption}</p>`;
                item.appendChild(caption);
            }
            
            carouselInner.appendChild(item);
        });
    }
}

// AJAX add story
const storyForm = document.getElementById('storyForm');
if(storyForm) {
    storyForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const submitBtn = storyForm.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;
        
        const formData = new FormData(this);
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('addStoryModal')).hide();
                // Clear the form
                storyForm.reset();
                if (imagePreview) {
                    imagePreview.style.display = 'none';
                }
                
                // If not on profile page, redirect
                if (!window.location.pathname.match(/\/profile\//)) {
                    window.location.href = '/profile/' + data.story.user_id;
                    return;
                }
                
                // Update stories bar in real time
                fetch(`/stories/user/${data.story.user_id}`)
                    .then(res => res.json())
                    .then(storyData => {
                        if (storyData.success) {
                            updateStoriesBar(storyData.stories);
                        }
                        if (submitBtn) submitBtn.disabled = false;
                    });
            } else {
                if (submitBtn) submitBtn.disabled = false;
                alert('Failed to create story. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error creating story:', error);
            if (submitBtn) submitBtn.disabled = false;
            alert('An error occurred while creating the story. Please try again.');
        });
    });
}

// Force manual-only carousel every time the stories modal is shown
// This guarantees no auto-advance, even if other scripts or attributes interfere

document.addEventListener('shown.bs.modal', function(event) {
    // Only target the stories modal
    if (event.target.id === 'storiesModal') {
        var carousel = document.getElementById('storiesCarousel');
        if (carousel) {
            // Get or create the Bootstrap Carousel instance
            var bsCarousel = bootstrap.Carousel.getOrCreateInstance(carousel);
            // Force manual mode
            bsCarousel.pause();
            bsCarousel._config.interval = false;
            bsCarousel._interval = null;
        }
    }
});

document.getElementById('storiesModal').addEventListener('show.bs.modal', function () {
    // Reset all bars
    document.querySelectorAll('#storiesCarousel .story-progress-bar').forEach(function(bar) {
        bar.style.transition = 'none';
        bar.style.width = '0%';
    });
    // Animate the active bar after a short delay
    setTimeout(animateStoryProgressBar, 50);
});
document.getElementById('storiesModal').addEventListener('hidden.bs.modal', function () {
    var backdrop = document.querySelector('.modal-backdrop');
    if (backdrop) backdrop.classList.remove('stories-blur');
});

function animateStoryProgressBar() {
    const activeItem = document.querySelector('#storiesCarousel .carousel-item.active');
    if (!activeItem) return;
    const bar = activeItem.querySelector('.story-progress-bar');
    if (!bar) return;

    // Step 1: Instantly reset bar and hide it
    bar.style.transition = 'none';
    bar.style.width = '0%';
    bar.style.opacity = '0';

    // Step 2: Wait for two animation frames to ensure the reset is rendered and hidden
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            // Step 3: Show the bar and animate to 100% over 10 seconds
            bar.style.transition = 'width 10s linear, opacity 0.1s';
            bar.style.opacity = '1';
            bar.style.width = '100%';
        });
    });
}

document.getElementById('storiesCarousel').addEventListener('slid.bs.carousel', animateStoryProgressBar);
</script>
@endpush
@endsection