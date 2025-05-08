<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Instagram Clone') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Dark mode styles */
        [data-bs-theme="dark"] {
            color-scheme: dark;
        }

        [data-bs-theme="dark"] body {
            background-color: #1a1a1a;
            color: #ffffff;
        }

        [data-bs-theme="dark"] .card {
            background-color: #2d2d2d;
            border-color: #404040;
        }

        [data-bs-theme="dark"] .navbar {
            background-color: #2d2d2d !important;
            border-bottom: 1px solid #404040;
        }

        [data-bs-theme="dark"] .navbar-light .navbar-brand,
        [data-bs-theme="dark"] .navbar-light .nav-link {
            color: #ffffff !important;
        }

        [data-bs-theme="dark"] .dropdown-menu {
            background-color: #2d2d2d;
            border-color: #404040;
        }

        [data-bs-theme="dark"] .dropdown-item {
            color: #ffffff;
        }

        [data-bs-theme="dark"] .dropdown-item:hover {
            background-color: #404040;
            color: #ffffff;
        }

        [data-bs-theme="dark"] .text-dark {
            color: #ffffff !important;
        }

        [data-bs-theme="dark"] .text-muted {
            color: #a0a0a0 !important;
        }

        [data-bs-theme="dark"] .btn-link {
            color: #ffffff;
        }

        [data-bs-theme="dark"] .btn-link:hover {
            color: #a0a0a0;
        }

        /* Theme toggle button styles */
        .theme-toggle {
            background: none;
            border: none;
            padding: 0.5rem;
            cursor: pointer;
            color: inherit;
        }

        .theme-toggle:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <a class="navbar-brand d-flex align-items-center" style="margin-left:3px; padding-left:0;" href="{{ url('/') }}">
                <img src="{{ asset('logo-maroc.png') }}" alt="instaMaroc Logo" style="height:36px; width:36px; object-fit:contain; margin-right:8px;">
                <span>insta<span style="color:green; font-weight:bold;">Ma</span><span style="color:red; font-weight:bold;">roc</span></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav me-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('posts.index') }}">
                                Feed
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link position-relative" href="{{ route('messages.index') }}">
                                <i class="fas fa-envelope fa-xl"></i>
                                @if($unreadMessages > 0)
                                    <span id="messagesBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        {{ $unreadMessages }}
                                        <span class="visually-hidden">unread messages</span>
                                    </span>
                                @endif
                            </a>
                        </li>
                    @endauth
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ms-auto">
                    <!-- Theme Toggle -->
                    <li class="nav-item">
                        <button class="theme-toggle nav-link" id="themeToggle">
                            <i class="fas fa-moon"></i>
                        </button>
                    </li>
                    <!-- Authentication Links -->
                    @guest
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                        @endif

                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('posts.create') }}">
                                <i class="fa fa-plus-square"></i> New Post
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->username }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('profile.show', Auth::user()->id) }}">
                                    Profile
                                </a>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('themeToggle');
            const html = document.documentElement;
            const icon = themeToggle.querySelector('i');

            // Check for saved theme preference
            const savedTheme = localStorage.getItem('theme') || 'light';
            html.setAttribute('data-bs-theme', savedTheme);
            updateIcon(savedTheme);

            // Toggle theme
            themeToggle.addEventListener('click', function() {
                const currentTheme = html.getAttribute('data-bs-theme');
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                
                html.setAttribute('data-bs-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateIcon(newTheme);
            });

            function updateIcon(theme) {
                icon.className = theme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
            }
        });
    </script>
</body>
</html>