@extends('layouts.app')

@section('title', 'Detail Jadwal Wawancara | MagangIn')

@section('content')
<div class="bg-white p-8 rounded-xl shadow-lg mx-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Detail Jadwal Wawancara</h1>

    <div class="grid grid-cols-2 gap-6">
        <div>
            <p class="text-gray-600 font-semibold">Tanggal</p>
            <p class="text-gray-800">
                {{ \Carbon\Carbon::parse($jadwal->tanggal_seleksi)->format('d M Y') }}
            </p>
        </div>
        <div>
            <p class="text-gray-600 font-semibold">Waktu</p>
            <p class="text-gray-800">
                {{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }} - 
                {{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }} WIB
            </p>
        </div>
        <div>
            <p class="text-gray-600 font-semibold">Pewawancara</p>
            <p class="text-gray-800">{{ $jadwal->pewawancara ?? '-' }}</p>
        </div>
        <div>
            <p class="text-gray-600 font-semibold">Tempat</p>
            <p class="text-gray-800">{{ $jadwal->tempat ?? '-' }}</p>
        </div>
    </div>

    {{-- Tambahan bagian peserta --}}
    <div class="mt-8">
        <p class="text-gray-600 font-semibold mb-2">Peserta yang Diwawancarai</p>

        @if($jadwal->pendaftarans && $jadwal->pendaftarans->count() > 0)
            <div class="border rounded-lg p-4 bg-gray-50">
                <ul class="list-disc list-inside space-y-2">
                    @foreach($jadwal->pendaftarans as $p)
                        <li>
                            <span class="font-semibold">{{ $p->user->name ?? 'Pendaftar #' . $p->id }}</span>
                            <span class="text-gray-600">({{ $p->infoOr->judul ?? '-' }})</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @else
            <p class="text-gray-500 italic">Belum ada peserta yang ditambahkan untuk jadwal ini.</p>
        @endif
    </div>

    <div class="mt-6 flex gap-3">
        <a href="{{ route('jadwal-seleksi.index') }}" 
           class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
            Kembali
        </a>
        <a href="{{ route('jadwal-seleksi.edit', $jadwal->id) }}" 
           class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
            Edit
        </a>
    </div>
</div>
@endsection
