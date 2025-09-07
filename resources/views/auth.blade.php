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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* Mengatur font agar lebih modern */
        body {
            font-family: 'Poppins', sans-serif;
        }
        /* Mengatur background poster untuk layar desktop */
        
    </style>
</head>
<body class="bg-baby-blue flex justify-center min-h-screen gap-x-10">

    <div class="w-[50%] overflow-y-auto pl-8">
        <img src="{{ asset('images/poster1.jpg') }}" alt="Poster MagangIn" class="w-full h-auto">
    </div>

    <!-- Kontainer utama untuk halaman login dan register, sekarang di kanan -->
    <div class="w-[38%] p-8 bg-white rounded-xl shadow-2xl space-y-8 mr-12 my-8">
        
        
        <!-- Bagian atas: Poster Informasi dengan gradasi warna logo -->
        <div class="max-w-xl p-8 bg-white rounded-xl space-y-8 mr-12 flex justify-center">
            <div class="flex items-center space-x-4">
                <img src="{{ asset('images/logomagangin.png') }}" alt="Logo MagangIn" style="width: 108px; height: 108px;" class="rounded-full border-2 border-white">
                <div class="pl-4">
                    <h2 class="text-3xl font-bold text-navy mb-2">Selamat Datang di MagangIn</h2>
                    <p class="text-md text-gray-500">Temukan kesempatan magang terbaik dan kembangkan potensimu bersama kami.</p>
                </div>
            </div>
        </div>
        
        <!-- Bagian tengah: Tombol Switch untuk Login/Register -->
        <div class="flex space-x-4">
            <button id="loginTab" class="py-2 px-4 rounded-full flex-1 font-semibold transition-colors duration-300 bg-baby-blue text-white hover:bg-baby-blue focus:outline-none">Login</button>
            <button id="registerTab" class="py-2 px-4 rounded-full flex-1 font-semibold transition-colors duration-300 bg-baby-blue text-white hover:bg-baby-blue focus:outline-none">Register</button>
        </div>

        <!-- Kontainer untuk form login dan register -->
        <div class="form-container">
            <!-- Form Login -->
            <form id="loginForm" action="{{ url('/penilaian') }}" method="GET" class="login-form space-y-6">
                <h2 class="text-2xl font-bold text-gray-800 text-center">Login</h2>
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                    <input type="text" id="username" name="username" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" name="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2">
                </div>
                <button type="submit" class="w-full py-3 px-4 rounded-full bg-navy text-white font-semibold hover:bg-baby-blue transition-colors duration-300">Login</button>
            </form>

            <!-- Form Register -->
            <form id="registerForm" class="register-form space-y-6 hidden">
                <h2 class="text-2xl font-bold text-gray-800 text-center">Register</h2>
                <div>
                    <label for="register-name" class="block text-sm font-medium text-gray-700">Nama</label>
                    <input type="text" id="register-name" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2">
                </div>
                <div>
                    <label for="register-nim" class="block text-sm font-medium text-gray-700">NIM</label>
                    <input type="text" id="register-nim" name="nim" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2">
                </div>
                <div>
                    <label for="register-telp" class="block text-sm font-medium text-gray-700">No. Telp</label>
                    <input type="tel" id="register-telp" name="telp" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2">
                </div>
                <div>
                    <label for="register-dinas" class="block text-sm font-medium text-gray-700">Dinas yang dipilih</label>
                    <input type="text" id="register-dinas" name="dinas" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2">
                </div>
                <div>
                    <label for="register-password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="register-password" name="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2">
                </div>
                <div>
                    <label for="register-confirm-password" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                    <input type="password" id="register-confirm-password" name="confirm-password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2">
                </div>
                <button type="submit" class="w-full py-3 px-4 rounded-full bg-navy text-white font-semibold hover:bg-baby-blue transition-colors duration-300">Register</button>
            </form>
        </div>
    </div>

    <script>
        const loginTab = document.getElementById('loginTab');
        const registerTab = document.getElementById('registerTab');
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');

        // Fungsi untuk mengaktifkan tab
        function setActiveTab(activeTab, inactiveTab, activeForm, inactiveForm) {
            activeTab.classList.remove('bg-navy', 'text-white');
            activeTab.classList.add('bg-gray-200', 'text-gray-700');
            inactiveTab.classList.remove('bg-gray-200', 'text-gray-700');
            inactiveTab.classList.add('bg-navy', 'text-white');
            
            activeForm.classList.remove('hidden');
            inactiveForm.classList.add('hidden');
        }

        loginTab.addEventListener('click', () => {
            setActiveTab(registerTab, loginTab, loginForm, registerForm);
        });

        registerTab.addEventListener('click', () => {
            setActiveTab(loginTab, registerTab, registerForm, loginForm);
        });

        // Menampilkan form login secara default saat halaman dimuat
        document.addEventListener('DOMContentLoaded', () => {
            setActiveTab(registerTab, loginTab, loginForm, registerForm);
        });
    </script>
</body>
</html>
