@extends('layouts.app')

@section('title', 'Detail Penilaian Wawancara | MagangIn')

@section('content')
<div class="bg-white p-8 rounded-2xl shadow-lg max-w-5xl mx-auto mt-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Detail Penilaian Wawancara</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Peserta --}}
        <div class="p-6 border rounded-lg bg-blue-50 shadow-sm">
            <p class="text-gray-600 font-semibold mb-2">Nama Peserta</p>
            <p class="text-gray-800 text-lg font-medium">{{ $penilaian->pendaftaran?->user?->nama_lengkap ?? '-' }}</p>
        </div>

        {{-- Dinas yang dipilih --}}
        <div class="p-6 border rounded-lg bg-green-50 shadow-sm">
            <p class="text-gray-600 font-semibold mb-2">Dinas yang dipilih</p>
            <p class="text-gray-800 text-md">
                <span class="font-medium">Dinas 1:</span> {{ $penilaian->pendaftaran?->dinasPilihan1?->nama_dinas ?? '-' }}<br>
                <span class="font-medium">Dinas 2:</span> {{ $penilaian->pendaftaran?->dinasPilihan2?->nama_dinas ?? '-' }}
            </p>
        </div>

        {{-- Pewawancara --}}
        <div class="p-6 border rounded-lg bg-yellow-50 shadow-sm">
            <p class="text-gray-600 font-semibold mb-2">Pewawancara</p>
            <p class="text-gray-800 text-lg font-medium">{{ $penilaian->jadwal?->pewawancara ?? '-' }}</p>
        </div>

        {{-- Nilai Komunikasi --}}
        <div class="p-6 border rounded-lg bg-gray-50 shadow-sm">
            <p class="text-gray-600 font-semibold mb-2">Nilai Komunikasi</p>
            <p class="text-gray-800 text-lg font-medium">{{ $penilaian->nilai_komunikasi ?? '-' }}</p>
        </div>

        {{-- Nilai Motivasi --}}
        <div class="p-6 border rounded-lg bg-gray-50 shadow-sm">
            <p class="text-gray-600 font-semibold mb-2">Nilai Motivasi</p>
            <p class="text-gray-800 text-lg font-medium">{{ $penilaian->nilai_motivasi ?? '-' }}</p>
        </div>

        {{-- Nilai Kemampuan --}}
        <div class="p-6 border rounded-lg bg-gray-50 shadow-sm">
            <p class="text-gray-600 font-semibold mb-2">Nilai Kemampuan</p>
            <p class="text-gray-800 text-lg font-medium">{{ $penilaian->nilai_kemampuan ?? '-' }}</p>
        </div>

        {{-- Nilai Total --}}
        <div class="p-6 border rounded-lg bg-gray-50 shadow-sm">
            <p class="text-gray-600 font-semibold mb-2">Nilai Total</p>
            <p class="text-gray-800 text-lg font-medium">{{ $penilaian->nilai_total ?? '-' }}</p>
        </div>

        {{-- Nilai Rata-Rata --}}
        <div class="p-6 border rounded-lg bg-gray-50 shadow-sm">
            <p class="text-gray-600 font-semibold mb-2">Nilai Rata-Rata</p>
            <p class="text-gray-800 text-lg font-medium">{{ $penilaian->nilai_rata_rata ?? '-' }}</p>
        </div>

        {{-- Status --}}
        <div class="p-6 border rounded-lg bg-gray-50 shadow-sm">
            <p class="text-gray-600 font-semibold mb-2">Status</p>
            @if($penilaian->status === 'sudah_dinilai')
                <span class="inline-block px-3 py-1 rounded-full bg-green-500 text-white text-sm font-medium">Sudah Dinilai</span>
            @else
                <span class="inline-block px-3 py-1 rounded-full bg-gray-400 text-white text-sm font-medium">Belum Dinilai</span>
            @endif
        </div>
    </div>

    {{-- Tombol Aksi --}}
    <div class="mt-8 flex gap-4">
        <a href="{{ route('penilaian-wawancara.index') }}" 
           class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">Kembali</a>
        <a href="{{ route('penilaian-wawancara.edit', $penilaian->id) }}" 
           class="px-6 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">Edit</a>
    </div>
</div>
@endsection
