<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MagangIn</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logomagangin.png') }}">
    @vite('resources/css/app.css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
</head>

<body
    class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-blue-100 font-[Poppins] relative">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8 relative">

        <!-- Tombol Kembali di dalam konten -->
        <a href="{{ route('login') }}"
            class="absolute top-4 left-4 flex items-center text-gray-600 hover:text-blue-600 transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </a>

        <!-- Header -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center mb-4">
                <img src="{{ asset('images/logomagangin.png') }}" alt="Logo MagangIn"
                    class="w-16 h-16 sm:w-20 sm:h-20 rounded-full shadow-md floating-animation">
            </div>
            <h1
                class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-navy to-baby-blue bg-clip-text text-transparent mb-2">
                MagangIn
            </h1>
        </div>

        <!-- Info -->
        <div class="mb-4 text-sm text-gray-600 text-center">
            {{ __('Lupa kata sandi Anda? Tidak masalah. Masukkan alamat email Anda dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi baru.') }}
        </div>

        <!-- Session Status -->
        @if (session('status'))
        <div class="mb-4 text-sm text-green-600 text-center">
            {{ session('status') }}
        </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="block w-full rounded-lg border-gray-300 focus:border-blue-400 focus:ring focus:ring-blue-200 transition">
                @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <div class="flex justify-end">
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    Reset Password
                </button>
            </div>
        </form>
    </div>

    <style>
    @keyframes float {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-6px);
        }
    }

    .floating-animation {
        animation: float 3s ease-in-out infinite;
    }
    </style>
</body>

</html>