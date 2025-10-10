@extends('layouts.app')

@section('title', 'Jadwal Seleksi Wawancara | MagangIn')

@section('content')
<div class="bg-white p-8 rounded-xl shadow-lg mx-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <h1 class="text-3xl font-bold text-gray-800">🗓️ Jadwal Seleksi Wawancara</h1>
    </div>

    @if($jadwals->isEmpty())
    <div class="text-center py-16">
        <img src="https://cdn-icons-png.flaticon.com/512/4076/4076505.png" class="w-32 mx-auto mb-4 opacity-80">
        <p class="text-gray-500 text-lg font-medium">Belum ada jadwal seleksi wawancara yang tersedia saat ini.</p>
    </div>
    @else
    <div class="space-y-8">
        @foreach($jadwals as $jadwal)
        <div
            class="relative bg-gradient-to-br from-blue-50 to-indigo-50 shadow-inner rounded-xl p-8 border border-blue-100 hover:shadow-lg transition-all duration-300">
            <div class="absolute top-5 right-6">
                <span class="px-4 py-2 text-sm font-medium bg-blue-100 text-blue-700 rounded-full shadow-sm">
                    {{ $jadwal->tanggal_seleksi->translatedFormat('d F Y') }}
                </span>
            </div>

            <h2 class="text-2xl font-bold text-gray-900 mb-8">
                {{ $jadwal->infoOr->judul }}
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-gray-700 text-base">
                <div>
                    <p class="font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-clock text-indigo-500 mr-2"></i> Jam
                    </p>
                    <p class="ml-6">
                        {{ $jadwal->waktu_mulai ? \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') : '-' }}
                        –
                        {{ $jadwal->waktu_selesai ? \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') : '-' }}
                        WIB
                    </p>
                </div>

                <div>
                    <p class="font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-map-marker-alt text-green-500 mr-2"></i> Tempat
                    </p>
                    <p class="ml-6">{{ $jadwal->tempat ?? '-' }}</p>
                </div>

                <div>
                    <p class="font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-user-tie text-yellow-500 mr-2"></i> Pewawancara
                    </p>
                    <p class="ml-6">{{ $jadwal->pewawancara ?? '-' }}</p>
                </div>

                <div>
                    <p class="font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-info-circle text-purple-500 mr-2"></i> Periode
                    </p>
                    <p class="ml-6">{{ $jadwal->infoOr->periode ?? '-' }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection