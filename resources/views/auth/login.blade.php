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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .fade-in {
            animation: fadeIn 0.8s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
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
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
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
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-navy opacity-5 rounded-full floating-animation" style="animation-delay: -3s;"></div>
    </div>

    <!-- Main Container -->
    <div class="relative w-full max-w-7xl mx-auto flex items-center justify-center gap-8 lg:gap-12">
        
        <!-- Poster Section - Hidden on mobile/tablet -->
        <div class="poster-container hidden lg:flex lg:w-1/2 xl:w-3/5 justify-center items-center">
            <div class="relative max-w-lg">
                <img src="{{ asset('images/poster1.jpg') }}" 
                     alt="Poster MagangIn" 
                     class="w-full h-auto rounded-3xl poster-shadow floating-animation">
            </div>
        </div>

        <!-- Form Section -->
        <div class="w-full lg:w-1/2 xl:w-2/5 max-w-lg mx-auto">
            <div class="glass-effect rounded-3xl shadow-2xl p-6 sm:p-8 fade-in max-h-[90vh] overflow-y-auto custom-scrollbar">
                
                <!-- Header Section -->
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center mb-4">
                        <img src="{{ asset('images/logomagangin.png') }}" 
                             alt="Logo MagangIn" 
                             class="w-16 h-16 sm:w-20 sm:h-20 rounded-full shadow-lg floating-animation">
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-navy to-baby-blue bg-clip-text text-transparent mb-2">
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
        class="flex-1 py-2 px-3 rounded-lg text-sm font-medium transition-all duration-300 text-gray-500 hover:text-navy">
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
                                <x-input-label for="email" :value="__('Email')" class="block text-sm font-medium text-gray-700 mb-2" />
                                <x-text-input id="email" 
                                            name="email" 
                                            type="email" 
                                            :value="old('email')" 
                                            required 
                                            autofocus 
                                            autocomplete="username"
                                            placeholder="masukkan email anda"
                                            class="w-full px-4 py-3 rounded-xl border-gray-300 focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 transition-all duration-300 input-focus" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="password" :value="__('Password')" class="block text-sm font-medium text-gray-700 mb-2" />
                                <div class="relative">
                                    <x-text-input id="password" 
                                                name="password" 
                                                type="password" 
                                                required 
                                                autocomplete="current-password"
                                                placeholder="masukkan password anda"
                                                class="w-full px-4 py-3 rounded-xl border-gray-300 focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 transition-all duration-300 input-focus pr-12" />
                                    <button type="button" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 toggle-password">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-sm">
                            <label for="remember_me" class="flex items-center">
                                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-baby-blue focus:ring-baby-blue" name="remember">
                                <span class="ml-2 text-gray-600">{{ __('Remember me') }}</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-baby-blue hover:text-navy transition-colors duration-300">
                                    {{ __('Forgot your password?') }}
                                </a>
                            @endif
                        </div>

                        <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-navy to-baby-blue text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 btn-hover">
                            {{ __('Log in') }}
                        </button>
                    </form>

                    <!-- Register Form -->
                    <form id="registerForm" method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="register-form space-y-4 hidden">
                        @csrf
                        
                        <!-- Step 1: Data Personal -->
                        <div id="step1" class="step-form">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 text-center">Data Personal</h3>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="register-nama" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                                    <input type="text" 
                                           id="register-nama" 
                                           name="nama_lengkap" 
                                           placeholder="Nama lengkap"
                                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-baby-blue focus:ring-0 transition-all duration-300 input-focus"
                                           required>
                                </div>
                                
                                <div>
                                    <label for="register-telp" class="block text-sm font-medium text-gray-700 mb-2">No. Telepon</label>
                                <input type="tel" 
                                       id="register-telp" 
                                       name="no_telp" 
                                       placeholder="08xxxxxxxxxx"
                                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-baby-blue focus:ring-0 transition-all duration-300 input-focus"
                                       required> 
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="register-email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <input type="email" 
                                           id="register-email" 
                                           name="email" 
                                           placeholder="email@example.com"
                                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-baby-blue focus:ring-0 transition-all duration-300 input-focus"
                                           required>
                                </div>
                                
                                <div>
                                    <label for="register-nim" class="block text-sm font-medium text-gray-700 mb-2">NIM</label>
                                    <input type="text" 
                                           id="register-nim" 
                                           name="nim" 
                                           placeholder="Nomor Induk Mahasiswa"
                                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-baby-blue focus:ring-0 transition-all duration-300 input-focus"
                                           required>
                                </div>
                            </div>

                  
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="register-password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                                    <div class="relative">
                                        <input type="password" 
                                               id="register-password" 
                                               name="password" 
                                               placeholder="Password"
                                               class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-baby-blue focus:ring-0 transition-all duration-300 input-focus pr-12"
                                               required>
                                        <button type="button" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 toggle-password">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="register-confirm-password" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password</label>
                                    <div class="relative">
                                        <input type="password" 
                                               id="register-confirm-password" 
                                               name="password_confirmation" 
                                               placeholder="Konfirmasi Password"
                                               class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-baby-blue focus:ring-0 transition-all duration-300 input-focus pr-12"
                                               required>
                                        <button type="button" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 toggle-password">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <button type="button" id="nextStep" class="w-full py-3 px-4 bg-gradient-to-r from-navy to-baby-blue text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 btn-hover">
                                Lanjut ke Pendaftaran Magang
                            </button>
                        </div>

                        <!-- Step 2: Data Magang -->
                        <div id="step2" class="step-form hidden">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 text-center">Pendaftaran Magang</h3>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="pilihan-dinas-1" class="block text-sm font-medium text-gray-700 mb-2">Pilihan Dinas 1 *</label>
                                    <select id="pilihan-dinas-1" 
                                            name="pilihan_dinas_1" 
                                            class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-baby-blue focus:ring-0 transition-all duration-300 input-focus"
                                            required>
                                        <option value="">Pilih Dinas Utama</option>
                                        <option value="1">Dinas hm</option>
                                        <option value="2">Dinas hm</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="pilihan-dinas-2" class="block text-sm font-medium text-gray-700 mb-2">Pilihan Dinas 2 (Opsional)</label>
                                    <select id="pilihan-dinas-2" 
                                            name="pilihan_dinas_2" 
                                            class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-baby-blue focus:ring-0 transition-all duration-300 input-focus">
                                        <option value="">Pilih Dinas Alternatif</option>
                                        <option value="1">Dinas hm</option>
                                        <option value="2">Dinas hm</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label for="motivasi" class="block text-sm font-medium text-gray-700 mb-2">Motivasi Magang *</label>
                                <textarea id="motivasi" 
                                          name="motivasi" 
                                          rows="4" 
                                          placeholder="Ceritakan motivasi Anda mengikuti program magang..."
                                          class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-baby-blue focus:ring-0 transition-all duration-300 input-focus resize-none"
                                          required></textarea>
                            </div>

                            <div>
                                <label for="pengalaman" class="block text-sm font-medium text-gray-700 mb-2">Pengalaman Terkait (Opsional)</label>
                                <textarea id="pengalaman" 
                                          name="pengalaman" 
                                          rows="3" 
                                          placeholder="Ceritakan pengalaman organisasi, kerja, atau proyek yang relevan..."
                                          class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-baby-blue focus:ring-0 transition-all duration-300 input-focus resize-none"></textarea>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="file-cv" class="block text-sm font-medium text-gray-700 mb-2">Upload CV *</label>
                                    <input type="file" 
                                           id="file-cv" 
                                           name="file_cv" 
                                           accept=".pdf,.doc,.docx"
                                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-baby-blue focus:ring-0 transition-all duration-300 input-focus"
                                           required>
                                    <p class="text-xs text-gray-500 mt-1">Format: PDF, DOC, DOCX (Max: 5MB)</p>
                                </div>
                                
                                <div>
                                    <label for="file-transkrip" class="block text-sm font-medium text-gray-700 mb-2">Upload Transkrip Nilai *</label>
                                    <input type="file" 
                                           id="file-transkrip" 
                                           name="file_transkrip" 
                                           accept=".pdf,.jpg,.jpeg,.png"
                                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-baby-blue focus:ring-0 transition-all duration-300 input-focus"
                                           required>
                                    <p class="text-xs text-gray-500 mt-1">Format: PDF, JPG, PNG (Max: 5MB)</p>
                                </div>
                            </div>

                            <div class="text-center">
                                <label class="flex items-center justify-center text-sm">
                                    <input type="checkbox" class="rounded border-gray-300 text-baby-blue focus:ring-baby-blue" required>
                                    <span class="ml-2 text-gray-600">Saya setuju dengan <a href="#" class="text-baby-blue hover:text-navy">syarat dan ketentuan</a> program magang</span>
                                </label>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <button type="button" id="prevStep" class="py-3 px-4 bg-gray-300 text-gray-700 font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                                    Kembali
                                </button>
                                <button type="submit" class="py-3 px-4 bg-gradient-to-r from-navy to-baby-blue text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 btn-hover">
                                    Daftar Sekarang
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const loginTab = document.getElementById('loginTab');
        const registerTab = document.getElementById('registerTab');
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');
        const step1 = document.getElementById('step1');
        const step2 = document.getElementById('step2');
        const nextStep = document.getElementById('nextStep');
        const prevStep = document.getElementById('prevStep');

        function setActiveTab(activeTab, inactiveTab, activeForm, inactiveForm) {
            // Reset semua tab
            [loginTab, registerTab].forEach(tab => {
                tab.classList.remove('bg-white', 'text-navy', 'shadow-md');
                tab.classList.add('text-gray-500');
            });
            
            // Set active tab
            activeTab.classList.remove('text-gray-500');
            activeTab.classList.add('bg-white', 'text-navy', 'shadow-md');
            
            // Toggle forms
            activeForm.classList.remove('hidden');
            inactiveForm.classList.add('hidden');
            
            // Reset to step 1 when switching to register
            if (activeForm === registerForm) {
                step1.classList.remove('hidden');
                step2.classList.add('hidden');
            }
            
            // Add fade in animation
            activeForm.classList.remove('fade-in');
            setTimeout(() => {
                activeForm.classList.add('fade-in');
            }, 10);
        }

        loginTab.addEventListener('click', () => {
            setActiveTab(loginTab, registerTab, loginForm, registerForm);
        });

        registerTab.addEventListener('click', () => {
            setActiveTab(registerTab, loginTab, registerForm, loginForm);
        });

        // Multi-step form navigation
        nextStep.addEventListener('click', () => {
            // Validate step 1 fields
            const step1Inputs = step1.querySelectorAll('input[required]');
            let isValid = true;
            
            step1Inputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add('border-red-500');
                } else {
                    input.classList.remove('border-red-500');
                }
            });
            
            // Check password confirmation
            const password = document.getElementById('register-password').value;
            const confirmPassword = document.getElementById('register-confirm-password').value;
            
            if (password !== confirmPassword) {
                isValid = false;
                document.getElementById('register-confirm-password').classList.add('border-red-500');
                alert('Password dan konfirmasi password tidak cocok!');
            }
            
            if (isValid) {
                step1.classList.add('hidden');
                step2.classList.remove('hidden');
                step2.classList.add('fade-in');
            } else {
                alert('Mohon lengkapi semua field yang wajib diisi!');
            }
        });

        prevStep.addEventListener('click', () => {
            step2.classList.add('hidden');
            step1.classList.remove('hidden');
            step1.classList.add('fade-in');
        });

        // Set default active tab
        document.addEventListener('DOMContentLoaded', () => {
            setActiveTab(loginTab, registerTab, loginForm, registerForm);
        });

        // Password toggle functionality
        document.addEventListener('click', (e) => {
            if (e.target.closest('.toggle-password')) {
                const button = e.target.closest('.toggle-password');
                const input = button.parentElement.querySelector('input');
                const isPassword = input.type === 'password';
                
                input.type = isPassword ? 'text' : 'password';
                
                const icon = button.querySelector('svg');
                if (isPassword) {
                    icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>';
                } else {
                    icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
                }
            }
        });

        // Prevent selecting same dinas for both options
        document.getElementById('pilihan-dinas-1').addEventListener('change', function() {
            const selectedValue = this.value;
            const dinas2Select = document.getElementById('pilihan-dinas-2');
            const dinas2Options = dinas2Select.querySelectorAll('option');
            
            // Reset all options
            dinas2Options.forEach(option => {
                option.disabled = false;
                option.style.display = 'block';
            });
            
            // Disable selected option in dinas 2
            if (selectedValue) {
                const optionToDisable = dinas2Select.querySelector(`option[value="${selectedValue}"]`);
                if (optionToDisable) {
                    optionToDisable.disabled = true;
                    optionToDisable.style.display = 'none';
                }
                
                // Reset dinas 2 if same value is selected
                if (dinas2Select.value === selectedValue) {
                    dinas2Select.value = '';
                }
            }
        });
        
        document.getElementById('pilihan-dinas-2').addEventListener('change', function() {
            const selectedValue = this.value;
            const dinas1Select = document.getElementById('pilihan-dinas-1');
            const dinas1Options = dinas1Select.querySelectorAll('option');
            
            // Reset all options
            dinas1Options.forEach(option => {
                option.disabled = false;
                option.style.display = 'block';
            });
            
            // Disable selected option in dinas 1
            if (selectedValue) {
                const optionToDisable = dinas1Select.querySelector(`option[value="${selectedValue}"]`);
                if (optionToDisable) {
                    optionToDisable.disabled = true;
                    optionToDisable.style.display = 'none';
                }
            }
        });

        // File upload validation
        document.getElementById('file-cv').addEventListener('change', function() {
            validateFileUpload(this, ['pdf', 'doc', 'docx'], 5);
        });

        document.getElementById('file-transkrip').addEventListener('change', function() {
            validateFileUpload(this, ['pdf', 'jpg', 'jpeg', 'png'], 5);
        });

        function validateFileUpload(input, allowedExtensions, maxSizeMB) {
            const file = input.files[0];
            if (!file) return;

            const fileName = file.name.toLowerCase();
            const fileExtension = fileName.split('.').pop();
            const fileSizeMB = file.size / (1024 * 1024);

            // Check file extension
            if (!allowedExtensions.includes(fileExtension)) {
                alert(`File harus berformat: ${allowedExtensions.join(', ').toUpperCase()}`);
                input.value = '';
                return;
            }

            // Check file size
            if (fileSizeMB > maxSizeMB) {
                alert(`Ukuran file maksimal ${maxSizeMB}MB`);
                input.value = '';
                return;
            }

            // Visual feedback for successful upload
            input.classList.add('border-green-500');
            setTimeout(() => {
                input.classList.remove('border-green-500');
            }, 2000);
        }

        // Form submission handling
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            // Additional validation before submit
            const requiredFields = this.querySelectorAll('input[required]:not([type="hidden"]), textarea[required], select[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim() && !field.files?.length) {
                    isValid = false;
                    field.classList.add('border-red-500');
                } else {
                    field.classList.remove('border-red-500');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Mohon lengkapi semua field yang wajib diisi!');
                return;
            }

            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Mendaftar...';

            // Reset button after 10 seconds if form doesn't submit successfully
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }, 10000);
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);

        // Character counter for textareas
        document.getElementById('motivasi').addEventListener('input', function() {
            updateCharacterCounter(this, 500, 'motivasi-counter');
        });

        document.getElementById('pengalaman').addEventListener('input', function() {
            updateCharacterCounter(this, 300, 'pengalaman-counter');
        });

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
            
            if (remaining < 50) {
                counter.classList.add('text-orange-500');
            } else {
                counter.classList.remove('text-orange-500');
            }
            
            if (remaining < 0) {
                counter.classList.add('text-red-500');
                counter.classList.remove('text-orange-500');
            } else {
                counter.classList.remove('text-red-500');
            }
        }

        // Smooth scroll for form navigation
        function smoothScrollToTop() {
            const formContainer = document.querySelector('.glass-effect');
            formContainer.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Add smooth scroll when switching steps
        nextStep.addEventListener('click', () => {
            setTimeout(smoothScrollToTop, 100);
        });

        prevStep.addEventListener('click', () => {
            setTimeout(smoothScrollToTop, 100);
        });
    </script>
</body>
</html>