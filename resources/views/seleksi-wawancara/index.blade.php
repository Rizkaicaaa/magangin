@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-8 px-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">
        Jadwal Seleksi Wawancara
    </h1>

    @if($jadwals->isEmpty())
        <div class="bg-white p-10 rounded-2xl shadow text-center text-gray-500">
            <i class="fas fa-calendar-times text-4xl mb-3 text-gray-400"></i>
            <p>Belum ada jadwal seleksi wawancara yang tersedia saat ini.</p>
        </div>
    @else
        <div class="space-y-8">
            @foreach($jadwals as $jadwal)
                <div class="relative bg-gradient-to-br from-blue-50 to-indigo-50 shadow-xl rounded-2xl p-8 border border-blue-100 hover:shadow-2xl transition-all duration-300">
                    
                    {{-- Tanggal di pojok kanan atas --}}
                    <div class="absolute top-5 right-6">
                        <span class="px-4 py-2 text-sm font-medium bg-blue-100 text-blue-700 rounded-full shadow-sm">
                            {{ $jadwal->tanggal_seleksi->translatedFormat('d F Y') }}
                        </span>
                    </div>

                    {{-- Judul --}}
                    <h2 class="text-2xl font-bold text-gray-900 mb-8">
                        {{ $jadwal->infoOr->judul }}
                    </h2>

                    {{-- Detail jadwal --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-gray-700 text-base">
                        
                        <div class="flex flex-col space-y-2">
                            <label class="font-semibold text-gray-800 flex items-center">
                                <i class="fas fa-clock text-indigo-500 mr-2"></i> Jam
                            </label>
                            <span class="ml-6">
                                {{ $jadwal->waktu_mulai ? \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') : '-' }}
                                – {{ $jadwal->waktu_selesai ? \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') : '-' }} WIB
                            </span>
                        </div>

                        <div class="flex flex-col space-y-2">
                            <label class="font-semibold text-gray-800 flex items-center">
                                <i class="fas fa-map-marker-alt text-green-500 mr-2"></i> Tempat
                            </label>
                            <span class="ml-6">{{ $jadwal->tempat ?? '-' }}</span>
                        </div>

                        <div class="flex flex-col space-y-2">
                            <label class="font-semibold text-gray-800 flex items-center">
                                <i class="fas fa-user-tie text-yellow-500 mr-2"></i> Pewawancara
                            </label>
                            <span class="ml-6">{{ $jadwal->pewawancara ?? '-' }}</span>
                        </div>

                        <div class="flex flex-col space-y-2">
                            <label class="font-semibold text-gray-800 flex items-center">
                                <i class="fas fa-info-circle text-purple-500 mr-2"></i> Periode
                            </label>
                            <span class="ml-6">{{ $jadwal->infoOr->periode ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
