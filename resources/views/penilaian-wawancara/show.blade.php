@extends('layouts.app')

@section('title', 'Detail Penilaian Wawancara | MagangIn')

@section('content')
<div class="bg-white p-8 rounded-xl shadow-lg mx-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Detail Penilaian Wawancara</h1>

    <div class="grid grid-cols-2 gap-6">
        <div>
            <p class="text-gray-600 font-semibold">Nama Peserta</p>
            <p class="text-gray-800">{{ $penilaian->pendaftaran?->user?->name ?? '-' }}</p>
        </div>
        <div>
            <p class="text-gray-600 font-semibold">Pewawancara</p>
            <p class="text-gray-800">{{ $penilaian->jadwal?->pewawancara ?? '-' }}</p>
        </div>
        <div>
            <p class="text-gray-600 font-semibold">Nilai Komunikasi</p>
            <p class="text-gray-800">{{ $penilaian->nilai_komunikasi }}</p>
        </div>
        <div>
            <p class="text-gray-600 font-semibold">Nilai Motivasi</p>
            <p class="text-gray-800">{{ $penilaian->nilai_motivasi }}</p>
        </div>
        <div>
            <p class="text-gray-600 font-semibold">Nilai Kemampuan</p>
            <p class="text-gray-800">{{ $penilaian->nilai_kemampuan }}</p>
        </div>
        <div>
            <p class="text-gray-600 font-semibold">Nilai Total</p>
            <p class="text-gray-800">{{ $penilaian->nilai_total }}</p>
        </div>
        <div>
            <p class="text-gray-600 font-semibold">Nilai Rata-Rata</p>
            <p class="text-gray-800">{{ $penilaian->nilai_rata_rata }}</p>
        </div>
        <div>
            <p class="text-gray-600 font-semibold">Status</p>
            @if($penilaian->status === 'sudah_dinilai')
                <span class="px-2 py-1 rounded-full bg-green-500 text-white text-sm">Sudah Dinilai</span>
            @else
                <span class="px-2 py-1 rounded-full bg-gray-400 text-white text-sm">Belum Dinilai</span>
            @endif
        </div>
    </div>

    <div class="mt-6 flex gap-3">
        <a href="{{ route('penilaian-wawancara.index') }}" 
           class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
            Kembali
        </a>
        <a href="{{ route('penilaian-wawancara.edit', $penilaian->id) }}" 
           class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
            Edit
        </a>
    </div>
</div>
@endsection
