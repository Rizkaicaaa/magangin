<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('description', 'Platform manajemen magang terpadu')">

    <title>@yield('title', 'MagangIn - Platform Magang Terpadu')</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logomagangin.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head-scripts')
    @yield('head')

    <style>
    html,
    body {
        margin: 0;
        padding: 0;
        width: 100%;
        max-width: 100vw;
        overflow-x: hidden;
        scroll-behavior: smooth;
        background: linear-gradient(to bottom right, #f9fafb, #f0f9ff);
    }

    /* Navbar sticky */
    .navbar-sticky {
        position: sticky;
        top: 0;
        z-index: 50;
        backdrop-filter: blur(12px);
        background: rgba(255, 255, 255, 0.8);
        border-bottom: 1px solid rgba(229, 231, 235, 0.6);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    /* Smooth transitions */
    * {
        transition: all 0.2s ease-in-out;
    }

    /* Tambahkan ini untuk Alpine.js agar elemen x-show tidak muncul sebelum inisialisasi */
    [x-cloak] {
        display: none !important;
    }

    /* Loading spinner */
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }

    /* Footer */
    footer {
        font-size: 0.8rem;
        background: #fff;
        border-top: 1px solid #e5e7eb;
        padding: 12px 0;
        color: #9ca3af;
    }

    footer a {
        color: #3b82f6;
        text-decoration: none;
    }

    footer a:hover {
        text-decoration: underline;
    }
    </style>
</head>

<body class="font-sans antialiased min-h-screen flex flex-col">

    <nav class="fixed top-0 left-0 w-full z-50 bg-white/80 backdrop-blur-md border-b border-gray-200 shadow-sm">
        @include('layouts.navigation')
    </nav>

    @if (isset($header))
    <header class="bg-white/80 backdrop-blur-sm shadow-sm py-4 px-6 w-full">
        <div class="max-w-7xl mx-auto">
            {{ $header }}
        </div>
    </header>
    @endif

    <main class="flex-1 w-full  mx-auto px-4 md:px-8 lg:px-12 py-8 pt-24">
        @yield('content')
    </main>

    <footer class="mt-auto text-center">
        <div class="max-w-7xl mx-auto px-4">
            &copy; {{ date('Y') }} <strong>MagangIn</strong>. All rights reserved.
        </div>
    </footer>

    <div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col gap-3 max-w-sm w-full"></div>

    <div id="loadingOverlay"
        class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-2xl p-8 flex flex-col items-center space-y-4 max-w-sm mx-4">
            <div class="relative">
                <div class="w-12 h-12 border-4 border-blue-200 rounded-full"></div>
                <div
                    class="absolute top-0 left-0 w-12 h-12 border-4 border-blue-600 rounded-full border-t-transparent animate-spin">
                </div>
            </div>
            <div class="text-center">
                <h3 class="text-lg font-semibold text-gray-900">Loading...</h3>
                <p class="text-sm text-gray-500">Please wait while we process your request</p>
            </div>
        </div>
    </div>

    <div id="successOverlay"
        class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-2xl p-8 flex flex-col items-center space-y-4 max-w-sm mx-4">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-check text-green-600 text-2xl"></i>
            </div>
            <div class="text-center">
                <h3 class="text-lg font-semibold text-gray-900">Success!</h3>
                <p class="text-sm text-gray-500">Operation completed successfully</p>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    // Enhanced Toast notification utility
    class ToastManager {
        static show(message, type = 'success', duration = 4000) {
            const colors = {
                success: {
                    background: 'linear-gradient(135deg, #10B981, #059669)',
                    icon: 'fas fa-check-circle'
                },
                error: {
                    background: 'linear-gradient(135deg, #EF4444, #DC2626)',
                    icon: 'fas fa-times-circle'
                },
                warning: {
                    background: 'linear-gradient(135deg, #F59E0B, #D97706)',
                    icon: 'fas fa-exclamation-triangle'
                },
                info: {
                    background: 'linear-gradient(135deg, #3B82F6, #2563EB)',
                    icon: 'fas fa-info-circle'
                }
            };

            const config = colors[type] || colors.success;

            Toastify({
                text: `<i class="${config.icon} mr-2"></i>${message}`,
                duration: duration,
                gravity: "top",
                position: "right",
                background: config.background,
                className: "rounded-lg font-medium shadow-lg border border-white/20",
                stopOnFocus: true,
                close: true,
                escapeMarkup: false,
                style: {
                    padding: "16px 20px",
                    fontSize: "14px",
                    boxShadow: "0 10px 25px rgba(0, 0, 0, 0.1), 0 4px 6px rgba(0, 0, 0, 0.05)"
                }
            }).showToast();
        }

        static success(message) {
            this.show(message, 'success');
        }

        static error(message) {
            this.show(message, 'error', 5000);
        }

        static warning(message) {
            this.show(message, 'warning', 4500);
        }

        static info(message) {
            this.show(message, 'info');
        }
    }

    // Enhanced Loading utilities
    class LoadingManager {
        static show(message = 'Loading...') {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) {
                overlay.querySelector('h3').textContent = message;
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        static hide() {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) {
                overlay.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }

        static showSuccess(message = 'Success!') {
            this.hide();
            const overlay = document.getElementById('successOverlay');
            if (overlay) {
                overlay.querySelector('h3').textContent = message;
                overlay.classList.remove('hidden');
                setTimeout(() => {
                    overlay.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }, 2000);
            }
        }
    }

    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Show session messages
        @if(session('success'))
        ToastManager.success("{{ session('success') }}");
        @endif

        @if(session('error'))
        ToastManager.error("{{ session('error') }}");
        @endif

        @if(session('warning'))
        ToastManager.warning("{{ session('warning') }}");
        @endif

        @if(session('info'))
        ToastManager.info("{{ session('info') }}");
        @endif

        // Enhanced form handling
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn && !submitBtn.disabled) {
                    submitBtn.disabled = true;
                    const originalContent = submitBtn.innerHTML;

                    // Add loading state
                    submitBtn.innerHTML = `
                        <div class="flex items-center justify-center">
                            <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                            Processing...
                        </div>
                    `;

                    // Reset after timeout as fallback
                    setTimeout(() => {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalContent;
                        }
                    }, 15000);
                }
            });
        });

        // Enhanced link handling with loading states
        document.querySelectorAll('a[href]').forEach(link => {
            // Skip external links and javascript links
            if (link.href.startsWith('http') && !link.href.includes(window.location.hostname)) return;
            if (link.href.startsWith('javascript:')) return;
            if (link.hasAttribute('target')) return;

            link.addEventListener('click', function(e) {
                // Add subtle loading indication for internal navigation
                const icon = link.querySelector('i');
                if (icon && !icon.classList.contains('fa-external-link-alt')) {
                    const originalClass = icon.className;
                    icon.className = 'fas fa-spinner fa-spin';

                    setTimeout(() => {
                        icon.className = originalClass;
                    }, 2000);
                }
            });
        });

        // Auto-hide alerts after some time
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 8000);
    });

    // Global error handler with better UX
    window.addEventListener('error', function(e) {
        console.error('Global error:', e.error);
        LoadingManager.hide();
        ToastManager.error('Terjadi kesalahan pada sistem. Silakan coba lagi.');
    });

    // Handle online/offline status
    window.addEventListener('online', () => {
        ToastManager.success('Koneksi internet tersambung kembali');
    });

    window.addEventListener('offline', () => {
        ToastManager.warning('Koneksi internet terputus');
    });

    // Make utilities globally available
    window.Toast = ToastManager;
    window.Loading = LoadingManager;

    // Enhanced keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Escape key to close modals
        if (e.key === 'Escape') {
            document.querySelectorAll('[x-data]').forEach(el => {
                const component = el.__x?.$data;
                if (component && component.showLogoutModal) {
                    component.showLogoutModal = false;
                }
                if (component && component.open) {
                    component.open = false;
                }
            });
        }
    });
    </script>

    @stack('scripts')
    @yield('scripts')
</body>

</html>