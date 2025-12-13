@extends('layouts.app')

@section('title', 'Dashboard | MagangIn')

@section('content')
<div class="bg-white p-6 md:p-8 rounded-xl shadow-lg mx-4 md:mx-6 space-y-8 md:space-y-10">

    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6 mb-8">

        <div class="w-full md:flex-1">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 break-words">
                📊 Dashboard
                @if($user->role == 'superadmin')
                <span class="block sm:inline mt-1 sm:mt-0">Superadmin</span>
                @elseif(in_array($user->role, ['admin']))
                <span class="block sm:inline mt-1 sm:mt-0">Admin
                    {{ $user->dinas ? '- ' . $user->dinas->nama_dinas : '' }}</span>
                @else
                <span class="block sm:inline mt-1 sm:mt-0">Mahasiswa</span>
                @endif
            </h1>
            <p class="text-sm md:text-base text-gray-500 mt-2">
                @if($user->role == 'superadmin')
                Ringkasan sistem magang BEM KM FTI Universitas Andalas
                @elseif(in_array($user->role, ['admin']))
                Kelola pendaftaran & kegiatan dinas Anda
                @else
                Pantau status pendaftaran & kegiatan magang Anda
                @endif
            </p>
            @if($selectedInfoOrData)
            <div class="mt-3">
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs md:text-sm bg-blue-100 text-blue-800 font-medium break-all">
                    📋 {{ $selectedInfoOrData->judul }} ({{ $selectedInfoOrData->periode }})
                </span>
            </div>
            @endif
        </div>

        @if($showFilter && $allInfoOr->count() > 0)
        <div class="w-full md:w-auto mt-2 md:mt-0">
            <form method="GET" action="{{ route('dashboard') }}"
                class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full">
                <label for="info_or_id"
                    class="text-sm font-medium text-gray-700 whitespace-nowrap hidden sm:block">Filter periode:</label>
                <label for="info_or_id" class="text-sm font-medium text-gray-700 sm:hidden mb-1">Pilih Periode:</label>

                <select name="info_or_id" id="info_or_id"
                    class="block w-full md:w-64 px-3 py-2 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                    onchange="this.form.submit()">
                    <option value="all" {{ $selectedInfoOr == 'all' ? 'selected' : '' }}>
                        🌐 Semua Periode
                    </option>
                    @foreach($allInfoOr as $info)
                    <option value="{{ $info->id }}" {{ $selectedInfoOr == $info->id ? 'selected' : '' }}>
                        {{ $info->judul }} ({{ $info->periode }})
                    </option>
                    @endforeach
                </select>
            </form>
        </div>
        @endif
    </div>

    <!-- Alert jika admin tidak memiliki dinas_id -->
    @if(in_array($user->role, ['admin']) && !$user->dinas_id)
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-red-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                    clip-rule="evenodd"></path>
            </svg>
            <p class="text-red-700">
                <strong>Peringatan:</strong> Akun admin Anda belum dikaitkan dengan dinas. Silakan hubungi superadmin
                untuk mengatur dinas Anda.
            </p>
        </div>
    </div>
    @endif

    <!-- Dashboard untuk Mahasiswa -->
    @if($user->role == 'mahasiswa')
    <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-2xl p-8 border border-blue-200">
        <div class="text-center">
            <h3 class="text-2xl font-bold text-gray-800 mb-2">
                Selamat Datang, {{ $user->nama_lengkap }}! 🎉
            </h3>
            <p class="text-gray-600 text-lg">
                Selamat datang di dashboard MagangIn. Pantau perkembangan magang Anda di sini.
            </p>
        </div>
    </div>

    <!-- Profile Information Card -->
    <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6">
            <h3 class="text-xl font-bold text-white flex items-center">
                <span class="mr-3">👤</span>
                Informasi Profil
            </h3>
        </div>
        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                            <span class="text-blue-600">📛</span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Nama Lengkap</p>
                            <p class="font-semibold text-gray-800">{{ $user->nama_lengkap ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                            <span class="text-green-600">🎓</span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">NIM</p>
                            <p class="font-semibold text-gray-800">{{ $user->nim ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                            <span class="text-purple-600">📧</span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Email</p>
                            <p class="font-semibold text-gray-800">{{ $user->email ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-4">
                            <span class="text-orange-600">📱</span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">No. Telepon</p>
                            <p class="font-semibold text-gray-800">{{ $user->no_telp ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Pendaftaran -->
    <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-8 py-6">
            <h3 class="text-xl font-bold text-white flex items-center">
                <span class="mr-3">📝</span>
                Status Pendaftaran Magang
            </h3>
        </div>
        <div class="p-8">
            @forelse($pendaftaranUser as $pendaftaran)
            <div class="mb-6 last:mb-0 border border-gray-200 rounded-xl p-6 bg-gradient-to-r from-gray-50 to-white">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Kolom Kiri -->
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <span class="text-blue-600 text-sm">📊</span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Status Pendaftaran</p>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    @if($pendaftaran->status_pendaftaran == 'terdaftar') bg-yellow-100 text-yellow-700 border border-yellow-200
                                    @elseif($pendaftaran->status_pendaftaran == 'lulus_wawancara') bg-green-100 text-green-700 border border-green-200
                                    @elseif($pendaftaran->status_pendaftaran == 'tidak_lulus_wawancara') bg-red-100 text-red-700 border border-red-200
                                    @elseif($pendaftaran->status_pendaftaran == 'lulus_magang') bg-emerald-100 text-emerald-700 border border-emerald-200
                                    @elseif($pendaftaran->status_pendaftaran == 'tidak_lulus_magang') bg-gray-100 text-gray-700 border border-gray-200
                                    @endif">
                                    @if($pendaftaran->status_pendaftaran == 'terdaftar') ⏳ Menunggu Seleksi
                                    @elseif($pendaftaran->status_pendaftaran == 'lulus_wawancara') ✅ Lulus Wawancara
                                    @elseif($pendaftaran->status_pendaftaran == 'tidak_lulus_wawancara') ❌ Tidak Lulus
                                    Wawancara
                                    @elseif($pendaftaran->status_pendaftaran == 'lulus_magang') 🎉 Lulus Magang
                                    @elseif($pendaftaran->status_pendaftaran == 'tidak_lulus_magang') 😔 Tidak Lulus
                                    Magang
                                    @else {{ ucfirst(str_replace('_', ' ', $pendaftaran->status_pendaftaran)) }}
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                                <span class="text-orange-600 text-sm">📅</span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Tanggal Pendaftaran</p>
                                <p class="font-semibold text-gray-800">
                                    {{ \Carbon\Carbon::parse($pendaftaran->tanggal_daftar)->format('d M Y') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Kanan -->
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                <span class="text-purple-600 text-sm">🏢</span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Pilihan Dinas 1</p>
                                <p class="font-semibold text-gray-800">
                                    {{ $pendaftaran->dinasPilihan1->nama_dinas ?? '-' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                <span class="text-indigo-600 text-sm">🏛️</span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Pilihan Dinas 2</p>
                                <p class="font-semibold text-gray-800">
                                    {{ $pendaftaran->dinasPilihan2->nama_dinas ?? '-' }}
                                </p>
                            </div>
                        </div>

                        @if($pendaftaran->dinasDiterima)
                        <div class="flex items-center p-3 bg-green-50 rounded-lg border border-green-200">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <span class="text-green-600 text-sm">🎯</span>
                            </div>
                            <div>
                                <p class="text-sm text-green-600 font-medium">Diterima di Dinas</p>
                                <p class="font-bold text-green-800">
                                    {{ $pendaftaran->dinasDiterima->nama_dinas }}
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                @if($pendaftaran->infoOr)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <span class="text-blue-600 text-sm">📋</span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Periode Magang</p>
                            <p class="font-semibold text-gray-800">
                                {{ $pendaftaran->infoOr->judul }} ({{ $pendaftaran->infoOr->periode }})
                            </p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @empty
            <div class="text-center py-12">
                <div class="w-24 h-24 bg-gray-100 rounded-full mx-auto flex items-center justify-center mb-4">
                    <span class="text-4xl text-gray-400">📝</span>
                </div>
                <h4 class="text-lg font-semibold text-gray-600 mb-2">Belum Ada Pendaftaran</h4>
                <p class="text-gray-500 mb-6">Anda belum mendaftar untuk program magang apapun.</p>
                <a href="{{ route('pendaftaran.create') }}"
                    class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-200">
                    <span class="mr-2">✨</span>
                    Daftar Sekarang
                </a>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Kegiatan Terdekat (jika ada) -->
    @if($kegiatanTerdekat && $kegiatanTerdekat->count() > 0)
    <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-8 py-6">
            <h3 class="text-xl font-bold text-white flex items-center">
                <span class="mr-3">📅</span>
                Kegiatan Terdekat
            </h3>
        </div>
        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($kegiatanTerdekat as $kegiatan)
                <div class="border border-gray-200 rounded-xl p-6 hover:shadow-md transition duration-200">
                    <div class="flex items-start">
                        <div
                            class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                            <span class="text-purple-600 text-xl">🎯</span>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-800 mb-2">{{ $kegiatan->nama_kegiatan }}</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center text-gray-600">
                                    <span class="mr-2">📅</span>
                                    {{ \Carbon\Carbon::parse($kegiatan->tanggal_kegiatan)->format('d M Y') }}
                                </div>
                                <div class="flex items-center text-gray-600">
                                    <span class="mr-2">📍</span>
                                    {{ $kegiatan->tempat }}
                                </div>
                                @if($kegiatan->deskripsi)
                                <div class="flex items-start text-gray-600">
                                    <span class="mr-2 mt-1">📝</span>
                                    <span>{{ Str::limit($kegiatan->deskripsi, 100) }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Motivational Message -->
    <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border-l-4 border-yellow-400 rounded-lg p-6">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                <span class="text-2xl">💪</span>
            </div>
            <div>
                <h4 class="text-lg font-bold text-gray-800 mb-1">Semangat dan Semoga Sukses!</h4>
                <p class="text-gray-600">
                    Terus berjuang dalam perjalanan magang Anda. Setiap langkah adalah pembelajaran berharga untuk masa
                    depan yang cerah! 🌟
                </p>
            </div>
        </div>
    </div>

    @else

    <!-- Statistik Ringkasan -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        @if($user->role == 'superadmin' || in_array($user->role, ['admin']))

        <!-- Total Pendaftar Card -->
        <div
            class="group relative bg-gradient-to-br from-blue-50 via-blue-100 to-blue-200 p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-blue-200">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center mb-3">
                        <div
                            class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-300">
                            <span class="text-white text-xl">👥</span>
                        </div>
                        <div>
                            <p class="text-sm text-blue-700 font-medium mb-1">
                                {{ $selectedInfoOr == 'all' ? 'Total Pendaftar' : 'Pendaftar Periode Ini' }}
                            </p>
                            @if(in_array($user->role, ['admin']))
                            <p class="text-xs text-blue-600">(Dinas Anda)</p>
                            @endif
                        </div>
                    </div>
                    <h2 class="text-4xl font-bold text-blue-800 mb-2 group-hover:text-blue-900 transition-colors">
                        {{ $totalPendaftar }}
                    </h2>
                </div>
                <div class="absolute top-4 right-4 opacity-20 group-hover:opacity-30 transition-opacity">
                    <span class="text-6xl text-blue-600">📊</span>
                </div>
            </div>
        </div>

        @if($user->role === 'superadmin')
        <!-- Total Dinas Card -->
        <div
            class="group relative bg-gradient-to-br from-emerald-50 via-emerald-100 to-emerald-200 p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-emerald-200">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center mb-3">
                        <div
                            class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-300">
                            <span class="text-white text-xl">🏢</span>
                        </div>
                        <div>
                            <p class="text-sm text-emerald-700 font-medium mb-1">
                                Total Dinas
                            </p>
                            <p class="text-xs text-emerald-600">Instansi Terdaftar</p>
                        </div>
                    </div>
                    <h2 class="text-4xl font-bold text-emerald-800 mb-2 group-hover:text-emerald-900 transition-colors">
                        {{ $totalDinas }}
                    </h2>
                </div>
                <div class="absolute top-4 right-4 opacity-20 group-hover:opacity-30 transition-opacity">
                    <span class="text-6xl text-emerald-600">🏛️</span>
                </div>
            </div>
        </div>
        @endif


        <!-- Total Kegiatan Card -->
        <div
            class="group relative bg-gradient-to-br from-orange-50 via-orange-100 to-orange-200 p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-orange-200">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center mb-3">
                        <div
                            class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-300">
                            <span class="text-white text-xl">📅</span>
                        </div>
                        <div>
                            <p class="text-sm text-orange-700 font-medium mb-1">
                                {{ $selectedInfoOr == 'all' ? 'Total Kegiatan' : 'Kegiatan Periode Ini' }}
                            </p>
                            <p class="text-xs text-orange-600">Jadwal Terdaftar</p>
                        </div>
                    </div>
                    <h2 class="text-4xl font-bold text-orange-800 mb-2 group-hover:text-orange-900 transition-colors">
                        {{ $totalKegiatan }}
                    </h2>
                </div>
                <div class="absolute top-4 right-4 opacity-20 group-hover:opacity-30 transition-opacity">
                    <span class="text-6xl text-orange-600">🎯</span>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Statistik Tambahan untuk Periode Tertentu -->
    @if(($user->role == 'superadmin' || in_array($user->role, ['admin'])) && $selectedInfoOr != 'all' &&
    !empty($additionalStats))
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-6">
            <h3 class="text-xl font-bold text-white flex items-center">
                <span class="mr-3">📈</span>
                Detail Status Pendaftaran - {{ $selectedInfoOrData->judul ?? 'Periode Terpilih' }}
            </h3>
            <p class="text-indigo-100 text-sm mt-1">Ringkasan lengkap status pendaftar untuk periode ini</p>
        </div>

        <div class="p-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                <!-- Menunggu Seleksi -->
                <div
                    class="group relative bg-gradient-to-br from-yellow-50 to-amber-100 p-6 rounded-xl border border-yellow-200 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="text-center">
                        <div
                            class="w-14 h-14 bg-yellow-400 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                            <span class="text-white text-2xl">⏳</span>
                        </div>
                        <p class="text-sm text-yellow-700 font-semibold mb-2">Menunggu Seleksi</p>
                        <h3 class="text-3xl font-bold text-yellow-800 mb-2">
                            {{ $additionalStats['terdaftar'] ?? 0 }}
                        </h3>
                    </div>
                </div>

                <!-- Lulus Wawancara -->
                <div
                    class="group relative bg-gradient-to-br from-lime-50 to-green-100 p-6 rounded-xl border border-lime-200 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="text-center">
                        <div
                            class="w-14 h-14 bg-lime-500 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                            <span class="text-white text-2xl">✅</span>
                        </div>
                        <p class="text-sm text-lime-700 font-semibold mb-2">Lulus Wawancara</p>
                        <h3 class="text-3xl font-bold text-lime-800 mb-2">
                            {{ $additionalStats['pendaftar_lulus_wawancara'] ?? 0 }}
                        </h3>
                    </div>
                </div>

                <!-- Ditolak -->
                <div
                    class="group relative bg-gradient-to-br from-red-50 to-pink-100 p-6 rounded-xl border border-red-200 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="text-center">
                        <div
                            class="w-14 h-14 bg-red-500 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                            <span class="text-white text-2xl">❌</span>
                        </div>
                        <p class="text-sm text-red-700 font-semibold mb-2">Ditolak</p>
                        <h3 class="text-3xl font-bold text-red-800 mb-2">
                            {{ $additionalStats['pendaftar_ditolak'] ?? 0 }}
                        </h3>
                    </div>
                </div>

                <!-- Lulus Magang -->
                <div
                    class="group relative bg-gradient-to-br from-emerald-50 to-teal-100 p-6 rounded-xl border border-emerald-200 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="text-center">
                        <div
                            class="w-14 h-14 bg-emerald-500 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                            <span class="text-white text-2xl">🎉</span>
                        </div>
                        <p class="text-sm text-emerald-700 font-semibold mb-2">Lulus Magang</p>
                        <h3 class="text-3xl font-bold text-emerald-800 mb-2">
                            {{ $additionalStats['pendaftar_lulus_magang'] ?? 0 }}
                        </h3>
                    </div>
                </div>

                <!-- Tidak Lulus Magang -->
                <div
                    class="group relative bg-gradient-to-br from-gray-50 to-slate-100 p-6 rounded-xl border border-gray-200 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="text-center">
                        <div
                            class="w-14 h-14 bg-gray-500 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                            <span class="text-white text-2xl">😔</span>
                        </div>
                        <p class="text-sm text-gray-700 font-semibold mb-2">Tidak Lulus Magang</p>
                        <h3 class="text-3xl font-bold text-gray-800 mb-2">
                            {{ $additionalStats['pendaftar_tidak_lulus_magang'] ?? 0 }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Grid Content berdasarkan Role untuk Admin & Superadmin -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">

        <!-- Pendaftar Terbaru -->
        <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-8 py-6">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <span class="mr-3">📝</span>
                    {{ $selectedInfoOr == 'all' ? 'Pendaftar Terbaru' : 'Pendaftar Periode Ini' }}
                </h3>
                @if(in_array($user->role, ['admin']))
                <p class="text-blue-100 text-sm mt-1">Data khusus untuk dinas Anda</p>
                @endif
            </div>

            <div class="p-6">
                @if($pendaftarTerbaru && count($pendaftarTerbaru) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">
                                    <div class="flex items-center">
                                        <span class="mr-2">👤</span>
                                        Nama Pendaftar
                                    </div>
                                </th>

                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">
                                    <div class="flex items-center">
                                        <span class="mr-2">📊</span>
                                        Status
                                    </div>
                                </th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">
                                    <div class="flex items-center">
                                        <span class="mr-2">📅</span>
                                        Tanggal
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($pendaftarTerbaru as $p)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="py-4 px-4">
                                    <div class="flex items-center">
                                        <div
                                            class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-white font-semibold text-sm">
                                                {{ strtoupper(substr($p->user->nama_lengkap ?? 'N', 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800">{{ $p->user->nama_lengkap ?? '-' }}
                                            </p>
                                            <p class="text-xs text-gray-500">{{ $p->user->nim ?? '' }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-4 px-4">
                                    @php
                                    $statusConfig = [
                                    'terdaftar' => ['bg' => 'bg-yellow-100 border-yellow-200 text-yellow-700', 'icon' =>
                                    '⏳'],
                                    'lulus_wawancara' => ['bg' => 'bg-green-100 border-green-200 text-green-700', 'icon'
                                    => '✅'],
                                    'tidak_lulus_wawancara' => ['bg' => 'bg-red-100 border-red-200 text-red-700', 'icon'
                                    => '❌'],
                                    'lulus_magang' => ['bg' => 'bg-emerald-100 border-emerald-200 text-emerald-700',
                                    'icon' => '🎉'],
                                    'tidak_lulus_magang' => ['bg' => 'bg-gray-100 border-gray-200 text-gray-700', 'icon'
                                    => '😔']
                                    ];
                                    $config = $statusConfig[$p->status_pendaftaran] ?? ['bg' => 'bg-gray-100
                                    border-gray-200 text-gray-700', 'icon' => '📋'];
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border {{ $config['bg'] }}">
                                        <span class="mr-1">{{ $config['icon'] }}</span>
                                        {{ ucfirst(str_replace('_', ' ', $p->status_pendaftaran)) }}
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <span class="mr-2">🗓️</span>
                                        {{ \Carbon\Carbon::parse($p->tanggal_daftar)->format('d M Y') }}
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gray-100 rounded-full mx-auto flex items-center justify-center mb-4">
                        <span class="text-3xl text-gray-400">📝</span>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-600 mb-2">
                        @if(in_array($user->role, ['admin']) && !$user->dinas_id)
                        Akun Belum Dikaitkan
                        @else
                        Belum Ada Pendaftar
                        @endif
                    </h4>
                    <p class="text-gray-500">
                        @if(in_array($user->role, ['admin']) && !$user->dinas_id)
                        Hubungi superadmin untuk mengatur dinas Anda
                        @else
                        {{ $selectedInfoOr == 'all' ? 'Belum ada pendaftar terdaftar' : 'Belum ada pendaftar untuk periode ini' }}
                        @endif
                    </p>
                </div>
                @endif
            </div>
        </div>

        <!-- Jadwal Kegiatan -->
        <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-8 py-6">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <span class="mr-3">📅</span>
                    {{ $selectedInfoOr == 'all' ? 'Jadwal Kegiatan Terdekat' : 'Kegiatan Periode Ini' }}
                </h3>
            </div>

            <div class="p-6">
                @if($kegiatanTerdekat && count($kegiatanTerdekat) > 0)
                <div class="space-y-4">
                    @foreach($kegiatanTerdekat as $index => $k)
                    <div
                        class="group relative border border-gray-200 rounded-xl p-6 hover:shadow-md hover:border-purple-200 transition-all duration-300">
                        <div class="flex items-start">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-purple-400 to-pink-500 rounded-xl flex items-center justify-center mr-4 flex-shrink-0 group-hover:scale-105 transition-transform duration-300">
                                <span class="text-white font-bold text-lg">{{ $index + 1 }}</span>
                            </div>
                            <div class="flex-1">
                                <h4
                                    class="font-bold text-gray-800 text-lg mb-2 group-hover:text-purple-700 transition-colors">
                                    {{ $k->nama_kegiatan }}
                                </h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                    <div class="flex items-center text-gray-600">
                                        <div
                                            class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                            <span class="text-blue-600">📅</span>
                                        </div>
                                        <div>
                                            <p class="font-medium">
                                                {{ \Carbon\Carbon::parse($k->tanggal_kegiatan)->format('d M Y') }}</p>
                                            <p class="text-xs text-gray-500">
                                                {{ \Carbon\Carbon::parse($k->tanggal_kegiatan)->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center text-gray-600">
                                        <div
                                            class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                            <span class="text-green-600">📍</span>
                                        </div>
                                        <div>
                                            <p class="font-medium">{{ $k->tempat }}</p>
                                            <p class="text-xs text-gray-500">Lokasi kegiatan</p>
                                        </div>
                                    </div>
                                </div>
                                @if($k->deskripsi)
                                <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                                    <p class="text-sm text-gray-600 leading-relaxed">
                                        {{ Str::limit($k->deskripsi, 120) }}</p>
                                </div>
                                @endif
                                @if($user->role == 'superadmin' && $k->infoOr)
                                <div class="mt-3">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-indigo-100 text-indigo-700 border border-indigo-200">
                                        <span class="mr-1">📋</span>
                                        {{ $k->infoOr->judul }}
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gray-100 rounded-full mx-auto flex items-center justify-center mb-4">
                        <span class="text-3xl text-gray-400">📅</span>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-600 mb-2">
                        @if(in_array($user->role, ['admin']) && !$user->dinas_id)
                        Akun Belum Dikaitkan
                        @else
                        Tidak Ada Kegiatan
                        @endif
                    </h4>
                    <p class="text-gray-500">
                        @if(in_array($user->role, ['admin']) && !$user->dinas_id)
                        Hubungi superadmin untuk mengatur dinas Anda
                        @else
                        {{ $selectedInfoOr == 'all' ? 'Tidak ada kegiatan terdekat' : 'Tidak ada kegiatan untuk periode ini' }}
                        @endif
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>

    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const userRole = "{{ $user['role'] ?? '' }}";

// Loading state untuk dropdown filter
document.getElementById('info_or_id')?.addEventListener('change', function() {
    const form = this.closest('form');
    const button = form.querySelector('button') || document.createElement('button');
    button.disabled = true;
    button.textContent = 'Loading...';
});

// Validate access untuk admin saat mengubah filter (opsional)
if (['admin'].includes(userRole)) {
    document.getElementById('info_or_id')?.addEventListener('change', function() {
        const infoOrId = this.value;

        if (infoOrId !== 'all') {
            // Validate access via AJAX
            fetch(`/api/dashboard/validate-info-or/${infoOrId}`)
                .then(response => response.json())
                .then(data => {
                    if (!data.can_access) {
                        alert('Anda tidak memiliki akses ke periode ini.');
                        this.value = 'all';
                        return false;
                    }
                    // Jika valid, submit form
                    this.form.submit();
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.form.submit(); // Submit anyway, biar server yang validasi
                });
        } else {
            this.form.submit();
        }
    });
} else {
    // Untuk superadmin dan mahasiswa, langsung submit
    document.getElementById('info_or_id')?.addEventListener('change', function() {
        this.form.submit();
    });
}
</script>
@endpush

@endsection