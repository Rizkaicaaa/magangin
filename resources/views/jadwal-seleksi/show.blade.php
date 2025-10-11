@extends('layouts.app')

@section('title', 'Detail Jadwal Wawancara | MagangIn')

@section('content')
<div class="bg-white p-8 rounded-2xl shadow-xl mx-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Detail Jadwal Wawancara</h1>

    {{-- Info Jadwal --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-blue-100 p-4 rounded-2xl shadow-sm">
            <p class="text-blue-700 font-semibold text-sm">Tanggal</p>
            <p class="text-blue-900 font-medium text-lg">
                {{ \Carbon\Carbon::parse($jadwal->tanggal_seleksi)->format('d M Y') }}
            </p>
        </div>
        <div class="bg-green-100 p-4 rounded-2xl shadow-sm">
            <p class="text-green-700 font-semibold text-sm">Waktu</p>
            <p class="text-green-900 font-medium text-lg">
                {{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }} -
                {{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }} WIB
            </p>
        </div>
        <div class="bg-yellow-100 p-4 rounded-2xl shadow-sm">
            <p class="text-yellow-800 font-semibold text-sm">Pewawancara</p>
            <p class="text-yellow-900 font-medium text-lg">{{ $jadwal->pewawancara ?? '-' }}</p>
        </div>
        <div class="bg-purple-100 p-4 rounded-2xl shadow-sm">
            <p class="text-purple-700 font-semibold text-sm">Tempat</p>
            <p class="text-purple-900 font-medium text-lg">{{ $jadwal->tempat ?? '-' }}</p>
        </div>
    </div>

    {{-- Peserta --}}
    <div class="mb-6">
        <p class="text-gray-600 font-semibold mb-2">Peserta yang Diwawancarai</p>

        @if($jadwal->pendaftaran)
        <div class="bg-pink-50 border border-pink-200 rounded-2xl p-4 shadow-sm hover:shadow-md transition duration-300">
            <p class="text-pink-800 font-medium text-lg">
                {{ $jadwal->pendaftaran->user->nama_lengkap ?? 'Nama tidak tersedia' }}
                <span class="text-pink-600 text-sm">— {{ $jadwal->pendaftaran->infoOr->judul ?? '-' }}</span>
            </p>
        </div>
        @else
        <p class="text-gray-500 italic">Belum ada peserta yang dipilih untuk jadwal ini.</p>
        @endif
    </div>

    {{-- Tombol --}}
    <div class="flex gap-3">
        <a href="{{ route('jadwal-seleksi.index') }}"
            class="px-5 py-2 bg-gray-500 text-white rounded-xl hover:bg-gray-600 transition duration-300 font-medium">
            Kembali
        </a>
        <a href="{{ route('jadwal-seleksi.edit', $jadwal->id) }}"
            class="px-5 py-2 bg-yellow-500 text-white rounded-xl hover:bg-yellow-600 transition duration-300 font-medium">
            Edit
        </a>
    </div>
</div>
@endsection
