@extends('layouts.app')

@section('title', 'Tambah Jadwal Wawancara | MagangIn')

@section('content')
<div class="bg-white p-10 rounded-3xl shadow-xl max-w-4xl mx-auto mt-8">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Tambah Jadwal Wawancara</h2>

    <form action="{{ route('jadwal-seleksi.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Info OR --}}
        <div class="bg-blue-50 p-4 rounded-2xl shadow-sm">
            <label class="block text-blue-700 font-semibold mb-2">Pilih Info OR</label>
            <select name="info_or_id" class="w-full border border-blue-200 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                <option value="" disabled selected>-- Pilih Info OR --</option>
                @foreach ($infos as $info)
                <option value="{{ $info->id }}">{{ $info->judul }}</option>
                @endforeach
            </select>
        </div>

        {{-- Tanggal Seleksi --}}
        <div class="bg-green-50 p-4 rounded-2xl shadow-sm">
            <label class="block text-green-700 font-semibold mb-2">Tanggal Seleksi</label>
            <input type="date" name="tanggal_seleksi" class="w-full border border-green-200 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-green-400" required>
        </div>

        {{-- Waktu --}}
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-yellow-50 p-4 rounded-2xl shadow-sm">
                <label class="block text-yellow-700 font-semibold mb-2">Waktu Mulai</label>
                <input type="time" name="waktu_mulai" class="w-full border border-yellow-200 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-yellow-400" required>
            </div>
            <div class="bg-orange-50 p-4 rounded-2xl shadow-sm">
                <label class="block text-orange-700 font-semibold mb-2">Waktu Selesai</label>
                <input type="time" name="waktu_selesai" class="w-full border border-orange-200 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-orange-400" required>
            </div>
        </div>

        {{-- Tempat --}}
        <div class="bg-purple-50 p-4 rounded-2xl shadow-sm">
            <label class="block text-purple-700 font-semibold mb-2">Tempat</label>
            <input type="text" name="tempat" placeholder="Masukkan lokasi wawancara" class="w-full border border-purple-200 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-purple-400" required>
        </div>

        {{-- Pewawancara --}}
        <div class="bg-pink-50 p-4 rounded-2xl shadow-sm">
            <label class="block text-pink-700 font-semibold mb-2">Nama Pewawancara</label>
            <input type="text" name="pewawancara" placeholder="Masukkan nama pewawancara" class="w-full border border-pink-200 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-pink-400" required>
        </div>

        {{-- Peserta Wawancara --}}
        <div class="bg-indigo-50 p-4 rounded-2xl shadow-sm">
            <label class="block text-indigo-700 font-semibold mb-2">Pilih Peserta Wawancara</label>
            <div id="peserta-container" class="border border-indigo-200 rounded-2xl p-4 max-h-64 overflow-y-auto bg-indigo-100">
                @foreach ($pendaftarans as $pendaftaran)
                <div class="flex items-center mb-2 px-2 py-2 rounded-lg hover:bg-indigo-200 transition peserta-item" data-info="{{ $pendaftaran->info_or_id }}">
                    <input type="radio" name="pendaftaran_id" value="{{ $pendaftaran->id }}" id="p{{ $pendaftaran->id }}" class="mr-3" {{ old('pendaftaran_id') == $pendaftaran->id ? 'checked' : '' }}>
                    <label for="p{{ $pendaftaran->id }}" class="text-indigo-900 font-medium">
                        {{ $pendaftaran->user->nama_lengkap ?? 'Nama tidak tersedia' }}
                        <span class="text-indigo-600 text-sm">— {{ $pendaftaran->infoOr->judul ?? '-' }}</span>
                    </label>
                </div>
                @endforeach

                <p id="no-peserta" class="text-indigo-500 p-2 hidden">Tidak ada peserta untuk Info OR ini.</p>
            </div>
        </div>

        {{-- Tombol --}}
        <div class="flex justify-end gap-3 mt-6">
            <a href="{{ route('jadwal-seleksi.index') }}" class="px-6 py-2 bg-gray-400 text-white rounded-2xl hover:bg-gray-500 transition font-medium">Batal</a>
            <button type="reset" class="px-6 py-2 bg-yellow-400 text-white rounded-2xl hover:bg-yellow-500 transition font-medium">Reset</button>
            <button type="submit" class="px-6 py-2 bg-navy text-white rounded-2xl hover:bg-baby-blue transition font-medium">Simpan</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const infoSelect = document.querySelector('select[name="info_or_id"]');
    const pesertaItems = document.querySelectorAll('.peserta-item');

    infoSelect.addEventListener('change', function() {
        const selectedInfoId = this.value;
        let hasPeserta = false;

        pesertaItems.forEach(item => {
            const itemInfo = item.getAttribute('data-info');
            if (itemInfo === selectedInfoId) {
                item.style.display = 'flex';
                hasPeserta = true;
            } else {
                item.style.display = 'none';
                item.querySelector('input').checked = false;
            }
        });

        document.getElementById('no-peserta').style.display = hasPeserta ? 'none' : 'block';
    });

    pesertaItems.forEach(item => item.style.display = 'none');
});
</script>
@endsection
