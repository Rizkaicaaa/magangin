<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'MagangIn')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logomagangin.png') }}">
    @vite('resources/css/app.css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Toastify CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    <!-- Toastify JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <style>
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f3f4f6;
    }
    </style>
</head>

<body class="bg-baby-blue">
    <header class="bg-navy text-white p-4 flex items-center shadow-lg flex justify-between">
        <div class="flex justify-between items-center w-full">
            <div class="flex items-center">
                <div class="bg-white rounded-full p-2 mr-4">
                    <img src="{{ asset('images/logomagangin.png') }}" alt="Logo MagangIn" class="h-10 w-10">
                </div>
                <h1 class="text-3xl font-bold">MagangIn</h1>
            </div>

            <button id="logoutButton"
                class="py-2 px-4 rounded-md font-semibold bg-baby-blue text-gray-700 hover:bg-gray-300">
                Logout
            </button>
        </div>
    </header>
    <nav class="bg-white text-white p-4 mb-6 flex items-center shadow-lg w-full flex space-x-4 mb-6">
        <a href="{{ url('/info-or') }}"
            class="py-2 px-4 rounded-md font-semibold @if(Request::is('info-or')) bg-navy text-white @else bg-gray-200 text-gray-700 hover:bg-baby-blue @endif">
            Info OR
        </a>
        <a href="{{ url('/pendaftar') }}"
            class="py-2 px-4 rounded-md font-semibold @if(Request::is('pendaftar')) bg-navy text-white @else bg-gray-200 text-gray-700 hover:bg-baby-blue @endif">
            Data Pendaftar
        </a>
       
        <a href="{{ url('/jadwal-kegiatan') }}"
            class="py-2 px-4 rounded-md font-semibold @if(Request::is('jadwal-kegiatan')) bg-navy text-white @else bg-gray-200 text-gray-700 hover:bg-baby-blue @endif">
            Data Kegiatan

        <a href="{{ url('/jadwal-seleksi') }}" class="py-2 px-4 rounded-md font-semibold @if(Request::is('jadwal-seleksi')) bg-navy text-white @else bg-gray-200 text-gray-700 hover:bg-baby-blue @endif">
            Kelola Jadwal Wawancara
        </a>
        <a href="{{ url('/penilaian') }}" class="py-2 px-4 rounded-md font-semibold @if(Request::is('penilaian')) bg-navy text-white @else bg-gray-200 text-gray-700 hover:bg-baby-blue @endif">
            Kelola Penilaian
        </a>
        <a href="{{ url('/hasilwawancara') }}" class="py-2 px-4 rounded-md font-semibold @if(Request::is('hasilwawancara')) bg-navy text-white @else bg-gray-200 text-gray-700 hover:bg-baby-blue @endif">
            Hasil Wawancara
        </a>
        
    </nav>

    @yield('content')

    <div id="logoutModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white p-6 rounded-lg shadow-xl text-center max-w-sm w-full">
            <p class="text-lg font-semibold text-gray-800 mb-4">Apakah Anda yakin ingin logout?</p>
            <div class="flex justify-center space-x-4">
                <button id="cancelLogout"
                    class="py-2 px-6 rounded-md bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 transition-colors duration-300">
                    Tidak
                </button>

                <!-- Gunakan form logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="py-2 px-6 rounded-md bg-navy text-white font-semibold hover:bg-baby-blue transition-colors duration-300">
                        Yakin
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
    // Ambil elemen modal dan tombol logout
    const logoutButton = document.getElementById('logoutButton');
    const logoutModal = document.getElementById('logoutModal');
    const cancelLogout = document.getElementById('cancelLogout');

    // Tampilkan modal saat tombol logout diklik
    logoutButton.addEventListener('click', () => {
        logoutModal.classList.remove('hidden');
    });

    // Sembunyikan modal saat tombol "Tidak" diklik
    cancelLogout.addEventListener('click', () => {
        logoutModal.classList.add('hidden');
    });
    </script>

    @yield('scripts')


    <div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col gap-3 max-w-sm w-full"></div>

</body>

</html>