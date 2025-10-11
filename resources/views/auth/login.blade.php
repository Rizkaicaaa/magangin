<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MagangIn</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logomagangin.png') }}">
    <!-- Menautkan ke file app.css yang dikompilasi oleh Vite -->
    @vite('resources/css/app.css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #87CEEB 0%, #e0f2fe 100%);
    }

    .glass-effect {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .floating-animation {
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-10px);
        }
    }

    .fade-in {
        animation: fadeIn 0.8s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .input-focus {
        transition: all 0.3s ease;
    }

    .input-focus:focus {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(135, 206, 235, 0.3);
    }

    .btn-hover {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-hover:before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s;
    }

    .btn-hover:hover:before {
        left: 100%;
    }

    .poster-shadow {
        filter: drop-shadow(0 25px 50px rgba(0, 0, 0, 0.15));
    }

    @media (max-width: 1024px) {
        .poster-container {
            display: none;
        }
    }

    /* Custom scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(135, 206, 235, 0.5);
        border-radius: 10px;
    }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">
    <!-- Background Elements -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-white opacity-10 rounded-full floating-animation"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-navy opacity-5 rounded-full floating-animation"
            style="animation-delay: -3s;"></div>
    </div>

    <!-- Main Container -->
    <div class="relative w-full max-w-7xl mx-auto flex items-center justify-center gap-8 lg:gap-12">

        <div class="poster-container hidden lg:flex lg:w-1/2 xl:w-3/5 justify-center items-center">

            <div class="info-list">
                @if($infoOr)
                    <div class="relative max-w-lg" x-data="{ open: false }">
                        <img src="{{ asset( $infoOr->gambar ) }}" 
                            alt="Poster MagangIn" 
                            class="w-full h-auto rounded-3xl poster-shadow floating-animation"/>

                        <button 
                            @click="open = true" 
                            class="absolute top-4 right-4 bg-white text-gray-800 rounded-full p-2 shadow-lg hover:bg-gray-100 transition duration-150"
                            title="Detail Informasi">
                            
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                            </svg>
                            
                        </button>

                        <div x-show="open" 
                            class="fixed inset-0 z-50 flex justify-center items-center" 
                            style="display: none;"> 
                            
                            <div x-show="open" 
                                x-transition:enter="ease-out duration-300" 
                                x-transition:enter-start="opacity-0" 
                                x-transition:enter-end="opacity-100" 
                                x-transition:leave="ease-in duration-200" 
                                x-transition:leave-start="opacity-100" 
                                x-transition:leave-end="opacity-0" 
                                @click="open = false" 
                                class="absolute inset-0 bg-black bg-opacity-50">
                            </div>

                            <div x-show="open" 
                                x-transition:enter="ease-out duration-300" 
                                x-transition:enter-start="opacity-0 scale-90" 
                                x-transition:enter-end="opacity-100 scale-100" 
                                x-transition:leave="ease-in duration-200" 
                                x-transition:leave-start="opacity-100 scale-100" 
                                x-transition:leave-end="opacity-0 scale-90" 
                                class="relative bg-white rounded-lg p-6 w-full mx-4 md:w-1/2 lg:w-2/5 shadow-xl z-50">
                                
                                <button @click="open = false" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                                
                                <h4 class="font-bold text-xl mb-4 text-gray-800 border-b pb-2">Detail {{ $infoOr->judul }}</h4>
                                
                                <div class="space-y-4">
                                    
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 mr-3 mt-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-700">Periode:</p>
                                            <p class="text-base text-gray-800">{{ $infoOr->periode }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 mr-3 mt-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-700">Deskripsi:</p>
                                            <p class="text-sm text-gray-800 mt-1">{{ ucwords(strtolower($infoOr->deskripsi)) }}</p> 
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 mr-3 mt-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-700">Persyaratan Umum:</p>
                                            <p class="text-sm text-gray-800 mt-1">{{ ucfirst(strtolower($infoOr->persyaratan_umum)) }}</p> 
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 mr-3 mt-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-700">Tanggal Pendaftaran:</p>
                                            <p class="text-sm text-gray-800 mt-1">
                                                {{ \Carbon\Carbon::parse($infoOr->tanggal_buka)->translatedFormat('j F Y') }}
                                                –
                                                {{ \Carbon\Carbon::parse($infoOr->tanggal_tutup)->translatedFormat('j F Y') }}
                                            </p> 
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 mr-3 mt-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.883 7.974 7.974 0 013.141-.617 7.975 7.975 0 013.141.617 3.42 3.42 0 001.946.883 4.238 4.238 0 013.743 4.086 4.239 4.239 0 01-.284 2.474 9.596 9.596 0 00-.734 3.176 9.596 9.596 0 00.734 3.176 4.238 4.238 0 01.284 2.474 3.42 3.42 0 00-1.946.883 7.974 7.974 0 01-3.141.617 7.975 7.975 0 01-3.141-.617 3.42 3.42 0 00-1.946-.883 4.238 4.238 0 01-3.743-4.086 4.239 4.239 0 01.284-2.474 9.596 9.596 0 00.734-3.176 9.596 9.596 0 00-.734-3.176 4.238 4.238 0 01-.284-2.474z"></path></svg>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-700">Status:</p>
                                            <p class="text-sm text-gray-800 mt-1">{{ ucfirst(strtolower($infoOr->status)) }}</p> 
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <p class="text-gray-500 text-sm">Belum ada informasi terbaru.</p>
                @endif
            </div>
        </div>

        <!-- Form Section -->
        <div class="w-full lg:w-1/2 xl:w-2/5 max-w-lg mx-auto">
            <div
                class="glass-effect rounded-3xl shadow-2xl p-6 sm:p-8 fade-in max-h-[90vh] overflow-y-auto custom-scrollbar">

                <!-- Header Section -->
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center mb-4">
                        <img src="{{ asset('images/logomagangin.png') }}" alt="Logo MagangIn"
                            class="w-16 h-16 sm:w-20 sm:h-20 rounded-full shadow-lg floating-animation">
                    </div>
                    <h1
                        class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-navy to-baby-blue bg-clip-text text-transparent mb-2">
                        MagangIn
                    </h1>
                    <p class="text-gray-600 text-sm sm:text-base">Temukan kesempatan magang terbaik</p>
                </div>

                <!-- Tab Buttons -->
                <div class="flex bg-gray-100 rounded-xl p-1 mb-4 w-full max-w-sm mx-auto">
                    <button id="loginTab"
                        class="flex-1 py-2 px-3 rounded-lg text-sm font-medium transition-all duration-300 bg-white text-navy shadow">
                        Login
                    </button>
                    <button id="registerTab"
                {{-- Atribut 'disabled' ditambahkan jika status BUKAN 'buka' --}}
                @if (strtolower($infoOr->status) != 'buka') disabled @endif
                
                class="flex-1 py-2 px-3 rounded-lg text-sm font-medium transition-all duration-300 
                    {{-- Styling tombol diubah berdasarkan status disabled --}}
                    @if (strtolower($infoOr->status) == 'buka')
                        text-gray-500 hover:text-navy hover:bg-white
                    @else
                        text-gray-400 bg-gray-200 cursor-not-allowed
                    @endif
                ">
                Register
            </button>
                </div>


                <!-- Forms Container -->
                <div class="form-container">
                    <!-- Login Form -->
                    <form id="loginForm" method="POST" action="{{ route('login') }}" class="login-form space-y-5">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="email" :value="__('Email')"
                                    class="block text-sm font-medium text-gray-700 mb-2" />
                                <x-text-input id="email" name="email" type="email" :value="old('email')" required
                                    autofocus autocomplete="username" placeholder="masukkan email anda"
                                    class="w-full px-4 py-3 rounded-xl border-gray-300 focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 transition-all duration-300 input-focus" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="password" :value="__('Password')"
                                    class="block text-sm font-medium text-gray-700 mb-2" />
                                <div class="relative">
                                    <x-text-input id="password" name="password" type="password" required
                                        autocomplete="current-password" placeholder="masukkan password anda"
                                        class="w-full px-4 py-3 rounded-xl border-gray-300 focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 transition-all duration-300 input-focus pr-12" />
                                    <button type="button"
                                        class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 toggle-password">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-sm">
                            <label for="remember_me" class="flex items-center">
                                <input id="remember_me" type="checkbox"
                                    class="rounded border-gray-300 text-baby-blue focus:ring-baby-blue" name="remember">
                                <span class="ml-2 text-gray-600">{{ __('Remember me') }}</span>
                            </label>
                            @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                                class="text-baby-blue hover:text-navy transition-colors duration-300">
                                {{ __('Forgot your password?') }}
                            </a>
                            @endif
                        </div>

                        <button type="submit"
                            class="w-full py-3 px-4 bg-gradient-to-r from-navy to-baby-blue text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 btn-hover">
                            {{ __('Log in') }}
                        </button>
                    </form>

                    <!-- Register Form -->
                    <form id="registerForm" method="POST" action="{{ route('register') }}" enctype="multipart/form-data"
                        class="register-form space-y-4 hidden">
                        @csrf

                        <!-- Step 1: Data Personal -->
                        <div id="step1" class="step-form">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 text-center">Data Personal</h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="register-nama" class="block text-sm font-medium text-gray-700 mb-2">Nama
                                        Lengkap</label>
                                    <input type="text" id="register-nama" name="nama_lengkap" placeholder="Nama lengkap"
                                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-baby-blue focus:ring-0 transition-all duration-300 input-focus"
                                        required minlength="3" maxlength="50" pattern="^[A-Za-z\s]+$"
                                        title="Nama hanya boleh huruf dan spasi, minimal 3 karakter">
                                </div>

                                <div>
                                    <label for="register-telp" class="block text-sm font-medium text-gray-700 mb-2">No.
                                        Telepon</label>
                                    <input type="tel" id="register-telp" name="no_telp" placeholder="08xxxxxxxxxx"
                                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-baby-blue focus:ring-0 transition-all duration-300 input-focus"
                                        required pattern="^[0-9]+$" minlength="12" maxlength="13"
                                        title="Nomor telepon hanya boleh angka, 12-13 digit.">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="register-email"
                                        class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <input type="email" id="register-email" name="email" placeholder="email@example.com"
                                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-baby-blue focus:ring-0 transition-all duration-300 input-focus"
                                        required maxlength="50">
                                </div>

                                <div>
                                    <label for="register-nim"
                                        class="block text-sm font-medium text-gray-700 mb-2">NIM</label>
                                    <input type="text" id="register-nim" name="nim" placeholder="Nomor Induk Mahasiswa"
                                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-baby-blue focus:ring-0 transition-all duration-300 input-focus"
                                        required pattern="^[0-9]+$" maxlength="10"
                                        title="NIM hanya boleh angka dan maksimal 10 digit.">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="register-password"
                                        class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                                    <div class="relative">
                                        <input type="password" id="register-password" name="password"
                                            placeholder="Password"
                                            class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-baby-blue focus:ring-0 transition-all duration-300 input-focus pr-12"
                                            required minlength="8" title="Password minimal 8 karakter.">
                                        <button type="button"
                                            class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 toggle-password">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <label for="register-confirm-password"
                                        class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password</label>
                                    <div class="relative">
                                        <input type="password" id="register-confirm-password"
                                            name="password_confirmation" placeholder="Konfirmasi Password"
                                            class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-baby-blue focus:ring-0 transition-all duration-300 input-focus pr-12"
                                            required minlength="8" title="Konfirmasi password minimal 8 karakter.">
                                        <button type="button"
                                            class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 toggle-password">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <button type="button" id="nextStep"
                                class="w-full mt-6 py-3 px-4 bg-gradient-to-r from-navy to-baby-blue text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 btn-hover">
                                Lanjut ke Pendaftaran Magang
                            </button>
                        </div>

                        <!-- Step 2: Data Magang -->
                        <div id="step2" class="step-form hidden">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 text-center">Pendaftaran Magang</h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="pilihan-dinas-1" class="block text-sm font-medium text-gray-700 mb-2">
                                        Pilihan Dinas 1 *
                                    </label>
                                    <select id="pilihan-dinas-1" name="pilihan_dinas_1" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 
                    focus:border-baby-blue focus:ring-0 transition-all duration-300 input-focus" required>
                                        <option value="">Pilih Dinas Utama</option>
                                        @foreach($allDinas as $dinas)
                                        <option value="{{ $dinas->id }}">{{ $dinas->nama_dinas }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="pilihan-dinas-2" class="block text-sm font-medium text-gray-700 mb-2">
                                        Pilihan Dinas 2 (Opsional)
                                    </label>
                                    <select id="pilihan-dinas-2" name="pilihan_dinas_2" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 
                    focus:border-baby-blue focus:ring-0 transition-all duration-300 input-focus">
                                        <option value="">Pilih Dinas Alternatif</option>
                                        @foreach($allDinas as $dinas)
                                        <option value="{{ $dinas->id }}">{{ $dinas->nama_dinas }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mt-4">
                                <label for="motivasi" class="block text-sm font-medium text-gray-700 mb-2">
                                    Motivasi *
                                </label>
                                <textarea id="motivasi" name="motivasi" rows="4" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 
                focus:border-baby-blue focus:ring-0 transition-all duration-300 input-focus" required minlength="20"
                                    title="Tuliskan motivasi minimal 20 karakter."></textarea>
                            </div>

                            <div class="mt-4">
                                <label for="pengalaman" class="block text-sm font-medium text-gray-700 mb-2">
                                    Pengalaman (Opsional)
                                </label>
                                <textarea id="pengalaman" name="pengalaman" rows="4" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 
                focus:border-baby-blue focus:ring-0 transition-all duration-300 input-focus" maxlength="300"
                                    title="Maksimal 300 karakter."></textarea>
                            </div>

                            <div class="mt-4">
                                <label for="file_cv" class="block text-sm font-medium text-gray-700 mb-2">
                                    Upload CV (PDF/DOC, max 5MB) *
                                </label>
                                <input type="file" id="file_cv" name="file_cv" class="w-full text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer 
                    focus:outline-none focus:border-baby-blue" required accept=".pdf,.doc,.docx"
                                    title="Hanya PDF atau DOC, maksimal 5MB.">
                            </div>

                            <div class="mt-4">
                                <label for="file_transkrip" class="block text-sm font-medium text-gray-700 mb-2">
                                    Upload Transkrip (PDF/JPG/PNG, max 5MB) *
                                </label>
                                <input type="file" id="file_transkrip" name="file_transkrip" class="w-full text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer 
                    focus:outline-none focus:border-baby-blue" required accept=".pdf,.jpg,.jpeg,.png"
                                    title="Hanya PDF, JPG, atau PNG. Maksimal 5MB.">
                            </div>

                            <div class="grid grid-cols-2 gap-4 mt-6">
                                <button type="button" id="prevStep"
                                    class="py-3 px-4 bg-gray-300 text-gray-700 font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                                    Kembali
                                </button>
                                <button type="submit"
                                    class="py-3 px-4 bg-gradient-to-r from-navy to-baby-blue text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 btn-hover">
                                    Daftar Sekarang
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>



</body>

</html>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    console.log("✅ Script login/register sudah dimuat.");

    // ======== ELEMENTS =========
    const loginTab = document.getElementById('loginTab');
    const registerTab = document.getElementById('registerTab');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const nextStep = document.getElementById('nextStep');
    const prevStep = document.getElementById('prevStep');
    const pilihanDinas1 = document.getElementById('pilihan-dinas-1');
    const pilihanDinas2 = document.getElementById('pilihan-dinas-2');
    const fileCV = document.getElementById('file_cv'); // Fixed ID
    const fileTranskrip = document.getElementById('file_transkrip'); // Fixed ID
    const motivasi = document.getElementById('motivasi');
    const pengalaman = document.getElementById('pengalaman');

    // ======== ERROR DISPLAY FUNCTION =========
    function showMessage(type, message, details = null) {
        // Remove existing messages
        const existingMessages = document.querySelectorAll('.alert-message');
        existingMessages.forEach(msg => msg.remove());

        const messageDiv = document.createElement('div');
        messageDiv.className = `alert-message mb-4 p-4 rounded-lg ${
            type === 'error' ? 'bg-red-100 text-red-700 border border-red-300' : 
            'bg-green-100 text-green-700 border border-green-300'
        }`;

        messageDiv.innerHTML = `
            <div class="font-semibold">${message}</div>
            ${details ? `<div class="text-sm mt-2">${details}</div>` : ''}
        `;

        // Insert at top of form container
        const formContainer = document.querySelector('.glass-effect');
        formContainer.insertBefore(messageDiv, formContainer.firstChild);

        // Auto remove after 8 seconds
        setTimeout(() => {
            if (messageDiv && messageDiv.parentNode) {
                messageDiv.remove();
            }
        }, 8000);

        console.log(`${type === 'error' ? '❌' : '✅'} Message:`, message, details);
    }

    function showFieldErrors(errors) {
        // Clear existing field errors
        const inputs = document.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.classList.remove('border-red-500');
            const errorDiv = input.parentNode.querySelector('.field-error');
            if (errorDiv) errorDiv.remove();
        });

        // Show new field errors
        Object.keys(errors).forEach(fieldName => {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (field) {
                field.classList.add('border-red-500');

                const errorDiv = document.createElement('div');
                errorDiv.className = 'field-error text-red-500 text-xs mt-1';
                errorDiv.textContent = Array.isArray(errors[fieldName]) ? errors[fieldName][0] : errors[
                    fieldName];

                field.parentNode.appendChild(errorDiv);
                console.warn(`⚠️ Field error [${fieldName}]:`, errors[fieldName]);
            }
        });
    }

    // ======== TAB SWITCH =========
    function setActiveTab(activeTab, inactiveTab, activeForm, inactiveForm) {
        console.log("🔄 Switch tab:", activeTab?.id);

        [loginTab, registerTab].forEach(tab => {
            tab?.classList.remove('bg-white', 'text-navy', 'shadow-md');
            tab?.classList.add('text-gray-500');
        });

        activeTab?.classList.remove('text-gray-500');
        activeTab?.classList.add('bg-white', 'text-navy', 'shadow-md');

        activeForm?.classList.remove('hidden');
        inactiveForm?.classList.add('hidden');

        if (activeForm === registerForm) {
            step1?.classList.remove('hidden');
            step2?.classList.add('hidden');
        }

        activeForm?.classList.remove('fade-in');
        setTimeout(() => {
            activeForm?.classList.add('fade-in');
        }, 10);
    }

    if (loginTab && registerTab) {
        loginTab.addEventListener('click', () => {
            console.log("👉 Klik loginTab");
            setActiveTab(loginTab, registerTab, loginForm, registerForm);
        });

        registerTab.addEventListener('click', () => {
            console.log("👉 Klik registerTab");
            setActiveTab(registerTab, loginTab, registerForm, loginForm);
        });

        // Default
        setActiveTab(loginTab, registerTab, loginForm, registerForm);
    }

    // ======== MULTI STEP =========
    if (nextStep) {
        nextStep.addEventListener('click', () => {
            console.log("👉 Klik nextStep");

            const step1Inputs = step1?.querySelectorAll('input[required]') || [];
            let isValid = true;
            const errors = {};

            step1Inputs.forEach(input => {
                const value = input.value.trim();
                const name = input.name;
                const pattern = input.getAttribute('pattern');
                const minLength = input.getAttribute('minlength');
                const maxLength = input.getAttribute('maxlength');

                // ✅ Cek required
                if (!value) {
                    isValid = false;
                    errors[name] = 'Field ini wajib diisi';
                    input.classList.add('border-red-500');
                    return;
                } else {
                    input.classList.remove('border-red-500');
                }

                // ✅ Cek pattern (misalnya hanya angka)
                if (pattern && !(new RegExp(pattern).test(value))) {
                    isValid = false;

                    // Buat pesan error default kalau tidak ada title
                    const defaultMsg = 'Format input tidak sesuai';
                    errors[name] = input.getAttribute('title') || defaultMsg;
                    input.classList.add('border-red-500');
                }

                // ✅ Cek minlength
                if (minLength && value.length < parseInt(minLength)) {
                    isValid = false;
                    errors[name] =
                        `${input.previousElementSibling?.innerText || 'Field'} minimal ${minLength} karakter`;
                    input.classList.add('border-red-500');
                }

                // ✅ Cek maxlength
                if (maxLength && value.length > parseInt(maxLength)) {
                    isValid = false;
                    errors[name] =
                        `${input.previousElementSibling?.innerText || 'Field'} maksimal ${maxLength} karakter`;
                    input.classList.add('border-red-500');
                }

                // ✅ Cek format email khusus
                if (input.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                    isValid = false;
                    errors[name] = 'Format email tidak valid';
                    input.classList.add('border-red-500');
                }
            });

            // ✅ Cek password match
            const password = document.getElementById('register-password')?.value;
            const confirmPassword = document.getElementById('register-confirm-password')?.value;

            if (password && confirmPassword && password !== confirmPassword) {
                isValid = false;
                errors.password_confirmation = 'Password dan konfirmasi password tidak cocok';
                document.getElementById('register-confirm-password')?.classList.add('border-red-500');
                console.warn("⚠️ Password mismatch!");
            }

            // ✅ Cek panjang minimal password
            if (password && password.length < 8) {
                isValid = false;
                errors.password = 'Password minimal 8 karakter';
                document.getElementById('register-password')?.classList.add('border-red-500');
            }

            // 🔐 Jika semua valid → lanjut ke step 2
            if (isValid) {
                step1?.classList.add('hidden');
                step2?.classList.remove('hidden');
                step2?.classList.add('fade-in');
                console.log("✅ Pindah ke step 2");
            } else {
                showFieldErrors(errors);
                showMessage('error', 'Mohon lengkapi semua field dengan benar!');
                console.warn("⚠️ Step 1 invalid", errors);
            }
        });
    }


    if (prevStep) {
        prevStep.addEventListener('click', () => {
            console.log("👉 Klik prevStep");
            step2?.classList.add('hidden');
            step1?.classList.remove('hidden');
            step1?.classList.add('fade-in');
        });
    }

    // ======== TOGGLE PASSWORD =========
    document.addEventListener('click', (e) => {
        if (e.target.closest('.toggle-password')) {
            const button = e.target.closest('.toggle-password');
            const input = button.parentElement.querySelector('input');
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';

            const icon = button.querySelector('svg');
            if (isPassword) {
                icon.innerHTML =
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>';
            } else {
                icon.innerHTML =
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
            }
            console.log("👁️ Toggle password", input.id);
        }
    });


    // ======== PILIHAN DINAS =========
    if (pilihanDinas1) {
        pilihanDinas1.addEventListener('change', function() {
            console.log("🔄 Pilihan dinas 1:", this.value);
            const dinas2Options = pilihanDinas2?.querySelectorAll('option') || [];
            dinas2Options.forEach(option => {
                option.disabled = false;
                option.style.display = 'block';
            });
            if (this.value) {
                const opt = pilihanDinas2?.querySelector(`option[value="${this.value}"]`);
                if (opt) {
                    opt.disabled = true;
                    opt.style.display = 'none';
                }
                if (pilihanDinas2?.value === this.value) {
                    pilihanDinas2.value = '';
                }
            }
        });
    }

    if (pilihanDinas2) {
        pilihanDinas2.addEventListener('change', function() {
            console.log("🔄 Pilihan dinas 2:", this.value);
            const dinas1Options = pilihanDinas1?.querySelectorAll('option') || [];
            dinas1Options.forEach(option => {
                option.disabled = false;
                option.style.display = 'block';
            });
            if (this.value) {
                const opt = pilihanDinas1?.querySelector(`option[value="${this.value}"]`);
                if (opt) {
                    opt.disabled = true;
                    opt.style.display = 'none';
                }
            }
        });
    }

    // ======== FILE UPLOAD VALIDATION =========
    function validateFileUpload(input, allowedExtensions, maxSizeMB) {
        const file = input.files[0];
        if (!file) return true;

        const fileName = file.name.toLowerCase();
        const fileExtension = fileName.split('.').pop();
        const fileSizeMB = file.size / (1024 * 1024);

        if (!allowedExtensions.includes(fileExtension)) {
            showMessage('error',
                `File ${input.name} harus berformat: ${allowedExtensions.join(', ').toUpperCase()}`);
            input.value = '';
            input.classList.add('border-red-500');
            console.warn("⚠️ Format file salah:", fileExtension);
            return false;
        }

        if (fileSizeMB > maxSizeMB) {
            showMessage('error',
                `Ukuran file ${input.name} maksimal ${maxSizeMB}MB (ukuran saat ini: ${fileSizeMB.toFixed(2)}MB)`
            );
            input.value = '';
            input.classList.add('border-red-500');
            console.warn("⚠️ File terlalu besar:", fileSizeMB.toFixed(2), "MB");
            return false;
        }

        input.classList.remove('border-red-500');
        input.classList.add('border-green-500');
        setTimeout(() => input.classList.remove('border-green-500'), 2000);
        console.log("✅ File upload valid:", file.name);
        return true;
    }

    fileCV?.addEventListener('change', () => validateFileUpload(fileCV, ['pdf', 'doc', 'docx'], 5));
    fileTranskrip?.addEventListener('change', () => validateFileUpload(fileTranskrip, ['pdf', 'jpg', 'jpeg',
        'png'
    ], 5));

    // ======== EMAIL VALIDATION =========
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // ======== FORM SUBMIT WITH AJAX =========
    // ======== FORM SUBMIT WITH IMPROVED DATA COLLECTION =========
    if (registerForm) {
        registerForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            console.log("🚀 Submit register form started");

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;

            // PERBAIKAN: Collect data manually untuk memastikan semua field terkirim
            const formElements = {
                // Step 1 - Data Personal
                nama_lengkap: document.getElementById('register-nama'),
                no_telp: document.getElementById('register-telp'),
                email: document.getElementById('register-email'),
                nim: document.getElementById('register-nim'),
                password: document.getElementById('register-password'),
                password_confirmation: document.getElementById('register-confirm-password'),

                // Step 2 - Data Magang
                pilihan_dinas_1: document.getElementById('pilihan-dinas-1'),
                pilihan_dinas_2: document.getElementById('pilihan-dinas-2'),
                motivasi: document.getElementById('motivasi'),
                pengalaman: document.getElementById('pengalaman'),
                file_cv: document.getElementById('file_cv'),
                file_transkrip: document.getElementById('file_transkrip')
            };

            // Validasi semua field yang ada
            let isValid = true;
            const fieldErrors = {};

            console.log("📋 Checking all form elements...");

            Object.keys(formElements).forEach(fieldName => {
                const element = formElements[fieldName];

                if (!element) {
                    console.error(`❌ Element not found: ${fieldName}`);
                    isValid = false;
                    fieldErrors[fieldName] = `Field ${fieldName} tidak ditemukan`;
                    return;
                }

                // Check required fields
                const isRequired = element.hasAttribute('required');
                let value;

                if (element.type === 'file') {
                    value = element.files.length > 0;
                } else {
                    value = element.value.trim();
                }

                if (isRequired && !value) {
                    isValid = false;
                    fieldErrors[fieldName] = 'Field ini wajib diisi';
                    element.classList.add('border-red-500');
                    console.warn(`⚠️ Required field empty: ${fieldName}`);
                } else {
                    element.classList.remove('border-red-500');
                }

                // Log field status
                if (element.type === 'file') {
                    console.log(
                        `  ${fieldName}: ${element.files.length > 0 ? element.files[0].name : 'No file'}`
                    );
                } else {
                    console.log(`  ${fieldName}: "${value}" (required: ${isRequired})`);
                }
            });

            // Additional validation
            const password = formElements.password?.value;
            const confirmPassword = formElements.password_confirmation?.value;

            if (password && confirmPassword && password !== confirmPassword) {
                isValid = false;
                fieldErrors.password_confirmation =
                    'Password dan konfirmasi password tidak cocok';
                formElements.password_confirmation?.classList.add('border-red-500');
                console.warn("⚠️ Password mismatch!");
            }

            if (password && password.length < 6) {
                isValid = false;
                fieldErrors.password = 'Password minimal 6 karakter';
                formElements.password?.classList.add('border-red-500');
            }

            // Email validation
            const email = formElements.email?.value;
            if (email && !isValidEmail(email)) {
                isValid = false;
                fieldErrors.email = 'Format email tidak valid';
                formElements.email?.classList.add('border-red-500');
            }

            // File validation
            if (formElements.file_cv?.files.length > 0) {
                if (!validateFileUpload(formElements.file_cv, ['pdf', 'doc', 'docx'], 5)) {
                    isValid = false;
                }
            }

            if (formElements.file_transkrip?.files.length > 0) {
                if (!validateFileUpload(formElements.file_transkrip, ['pdf', 'jpg', 'jpeg',
                            'png'
                        ],
                        5)) {
                    isValid = false;
                }
            }

            if (!isValid) {
                showFieldErrors(fieldErrors);
                showMessage('error',
                    'Mohon lengkapi semua field yang wajib diisi dengan benar!');
                console.warn("⚠️ Register form tidak valid", fieldErrors);
                return;
            }

            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Mendaftar...
        `;

            try {
                // PERBAIKAN: Manual FormData creation untuk memastikan semua field ada
                const formData = new FormData();

                // Add text fields
                const textFields = ['nama_lengkap', 'no_telp', 'email', 'nim', 'password',
                    'password_confirmation',
                    'pilihan_dinas_1', 'pilihan_dinas_2', 'motivasi', 'pengalaman'
                ];

                textFields.forEach(fieldName => {
                    const element = formElements[fieldName];
                    if (element && element.value) {
                        formData.append(fieldName, element.value.trim());
                        console.log(`✅ Added ${fieldName}: "${element.value.trim()}"`);
                    } else if (fieldName === 'pilihan_dinas_2' || fieldName ===
                        'pengalaman' || fieldName === 'no_telp') {
                        // Optional fields
                        formData.append(fieldName, '');
                        console.log(`➖ Added optional ${fieldName}: ""`);
                    } else {
                        console.warn(`⚠️ Missing required field: ${fieldName}`);
                    }
                });

                // Add file fields
                const fileFields = ['file_cv', 'file_transkrip'];
                fileFields.forEach(fieldName => {
                    const element = formElements[fieldName];
                    if (element && element.files.length > 0) {
                        formData.append(fieldName, element.files[0]);
                        console.log(
                            `📎 Added file ${fieldName}: ${element.files[0].name} (${element.files[0].size} bytes)`
                        );
                    } else {
                        console.warn(`⚠️ Missing file: ${fieldName}`);
                    }
                });

                // Add CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')
                    ?.getAttribute(
                        'content') ||
                    document.querySelector('input[name="_token"]')?.value;

                if (csrfToken) {
                    formData.append('_token', csrfToken);
                    console.log("🔐 Added CSRF token");
                } else {
                    console.warn("⚠️ No CSRF token found");
                }

                // Log final FormData
                console.log("📝 Final FormData entries:");
                for (let [key, value] of formData.entries()) {
                    if (value instanceof File) {
                        console.log(`  ${key}: File(${value.name}, ${value.size} bytes)`);
                    } else {
                        console.log(`  ${key}: "${value}"`);
                    }
                }

                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });

                console.log("📡 Response status:", response.status);
                console.log("📡 Response headers:", Object.fromEntries(response.headers
                    .entries()));

                const contentType = response.headers.get('content-type');
                let data;

                if (contentType && contentType.includes('application/json')) {
                    data = await response.json();
                    console.log("📦 JSON Response:", data);
                } else {
                    const text = await response.text();
                    console.log("📄 Text Response:", text.substring(0, 500) + (text.length >
                        500 ?
                        '...' : ''));

                    // Try to parse JSON from text response
                    try {
                        data = JSON.parse(text);
                    } catch {
                        data = {
                            success: false,
                            message: 'Server error: Invalid response format',
                            debug: text
                        };
                    }
                }

                if (response.ok && data.success) {
                    console.log("✅ Registration successful!");
                    showMessage('success',
                        data.message || 'Pendaftaran berhasil!',
                        data.data ? `Email: ${data.data.email}, NIM: ${data.data.nim}` :
                        null
                    );

                    // Reset form
                    this.reset();

                    // Switch to login tab after 2 seconds
                    setTimeout(() => {
                        setActiveTab(loginTab, registerTab, loginForm, registerForm);
                        showMessage('success',
                            'Silakan login dengan akun yang baru Anda buat.');
                    }, 2000);

                } else {
                    console.error("❌ Registration failed:", data);

                    if (data.errors && typeof data.errors === 'object') {
                        showFieldErrors(data.errors);
                    }

                    showMessage('error',
                        data.message || 'Terjadi kesalahan saat mendaftar',
                        data.debug ? `Debug: ${data.debug}` : null
                    );
                }

            } catch (error) {
                console.error("💥 Network/Parse Error:", error);
                showMessage('error',
                    'Terjadi kesalahan jaringan. Periksa koneksi internet Anda.',
                    `Error: ${error.message}`
                );
            } finally {
                // Restore button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    }

    // ======== CHARACTER COUNTER =========
    function updateCharacterCounter(textarea, maxLength, counterId) {
        let counter = document.getElementById(counterId);
        if (!counter) {
            counter = document.createElement('div');
            counter.id = counterId;
            counter.className = 'text-xs text-gray-500 mt-1 text-right';
            textarea.parentNode.appendChild(counter);
        }
        const remaining = maxLength - textarea.value.length;
        counter.textContent = `${textarea.value.length}/${maxLength} karakter`;

        counter.classList.toggle('text-orange-500', remaining < 50 && remaining >= 0);
        counter.classList.toggle('text-red-500', remaining < 0);
    }

    motivasi?.addEventListener('input', () => updateCharacterCounter(motivasi, 500, 'motivasi-counter'));
    pengalaman?.addEventListener('input', () => updateCharacterCounter(pengalaman, 300,
        'pengalaman-counter'));

    // ======== SMOOTH SCROLL =========
    function smoothScrollToTop() {
        const formContainer = document.querySelector('.glass-effect');
        formContainer?.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    nextStep?.addEventListener('click', () => setTimeout(smoothScrollToTop, 100));
    prevStep?.addEventListener('click', () => setTimeout(smoothScrollToTop, 100));

});
</script>