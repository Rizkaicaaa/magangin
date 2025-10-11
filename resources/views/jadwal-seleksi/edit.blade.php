@extends('layouts.app')

@section('title', 'Edit Jadwal Wawancara | MagangIn')

@section('content')
<div class="bg-white p-8 rounded-2xl shadow-xl mx-6">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Edit Jadwal Wawancara</h2>

    <form action="{{ route('jadwal-seleksi.update', $jadwalSeleksi->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Pilih Info OR --}}
        <div class="bg-blue-50 p-4 rounded-2xl shadow-sm">
            <label class="block text-blue-700 font-semibold mb-1">Pilih Info OR</label>
            <select name="info_or_id"
                class="w-full border border-blue-200 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                required>
                <option value="" disabled>-- Pilih Info OR --</option>
                @foreach ($infos as $info)
                <option value="{{ $info->id }}" {{ $jadwalSeleksi->info_or_id == $info->id ? 'selected' : '' }}>
                    {{ $info->judul }}
                </option>
                @endforeach
            </select>
        </div>

        {{-- Tanggal dan Waktu --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-green-50 p-4 rounded-2xl shadow-sm">
                <label class="block text-green-700 font-semibold mb-1">Tanggal Seleksi</label>
                <input type="date" name="tanggal_seleksi"
                    value="{{ \Carbon\Carbon::parse($jadwalSeleksi->tanggal_seleksi)->format('Y-m-d') }}"
                    class="w-full border border-green-200 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-green-400"
                    required>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-yellow-50 p-4 rounded-2xl shadow-sm">
                    <label class="block text-yellow-700 font-semibold mb-1">Waktu Mulai</label>
                    <input type="time" name="waktu_mulai"
                        value="{{ \Carbon\Carbon::parse($jadwalSeleksi->waktu_mulai)->format('H:i') }}"
                        class="w-full border border-yellow-200 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-yellow-400"
                        required>
                </div>
                <div class="bg-orange-50 p-4 rounded-2xl shadow-sm">
                    <label class="block text-orange-700 font-semibold mb-1">Waktu Selesai</label>
                    <input type="time" name="waktu_selesai"
                        value="{{ \Carbon\Carbon::parse($jadwalSeleksi->waktu_selesai)->format('H:i') }}"
                        class="w-full border border-orange-200 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-orange-400"
                        required>
                </div>
            </div>
        </div>

        {{-- Tempat dan Pewawancara --}}
        <div class="bg-purple-50 p-4 rounded-2xl shadow-sm">
            <label class="block text-purple-700 font-semibold mb-1">Tempat</label>
            <input type="text" name="tempat" value="{{ $jadwalSeleksi->tempat }}"
                class="w-full border border-purple-200 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-purple-400"
                required>
        </div>

        <div class="bg-pink-50 p-4 rounded-2xl shadow-sm">
            <label class="block text-pink-700 font-semibold mb-1">Nama Pewawancara</label>
            <input type="text" name="pewawancara" value="{{ $jadwalSeleksi->pewawancara }}"
                class="w-full border border-pink-200 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-pink-400"
                required>
        </div>

        {{-- Pilih Peserta --}}
        <div class="bg-indigo-50 p-4 rounded-2xl shadow-sm">
            <label class="block text-indigo-700 font-semibold mb-2">Pilih Peserta Wawancara</label>
            <div id="peserta-container" class="border border-indigo-200 rounded-2xl p-4 max-h-64 overflow-y-auto bg-indigo-100">
                @foreach($pendaftarans as $p)
                <div class="flex items-center mb-2 peserta-item p-2 rounded-lg hover:bg-indigo-200 transition duration-200"
                    data-info="{{ $p->info_or_id }}">
                    <input type="radio" name="pendaftaran_id" value="{{ $p->id }}" id="p{{ $p->id }}" class="mr-3"
                        {{ (old('pendaftaran_id') ?? $jadwalSeleksi->pendaftaran_id) == $p->id ? 'checked' : '' }}>
                    <label for="p{{ $p->id }}" class="text-indigo-900 font-medium">
                        {{ $p->user->nama_lengkap ?? 'Nama tidak tersedia' }}
                        <span class="text-indigo-600 text-sm">— {{ $p->infoOr->judul ?? '-' }}</span>
                    </label>
                </div>
                @endforeach

                <p id="no-peserta" class="text-indigo-500 p-2" style="display:none">Tidak ada peserta untuk Info OR ini.</p>
            </div>
        </div>

        {{-- Tombol --}}
        <div class="flex justify-end gap-3 mt-6">
            <a href="{{ route('jadwal-seleksi.index') }}"
                class="px-6 py-2 bg-gray-400 text-white rounded-2xl hover:bg-gray-500 transition duration-300 font-medium">
                Batal
            </a>
            <button type="submit"
                class="px-6 py-2 bg-navy text-white rounded-2xl hover:bg-baby-blue transition duration-300 font-medium">
                Update
            </button>
        </div>
    </form>
</div>

{{-- SCRIPT FILTER PESERTA BERDASARKAN INFO OR --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const infoSelect = document.querySelector('select[name="info_or_id"]');
    const pesertaItems = document.querySelectorAll('.peserta-item');
    const selectedInfo = infoSelect.value;

    pesertaItems.forEach(item => {
        const itemInfo = item.getAttribute('data-info');
        const checkbox = item.querySelector('input');
        item.style.display = (itemInfo === selectedInfo) ? 'flex' : 'none';
        if(itemInfo !== selectedInfo) checkbox.checked = false;
    });

    infoSelect.addEventListener('change', function() {
        const selectedInfoId = this.value;
        pesertaItems.forEach(item => {
            const itemInfo = item.getAttribute('data-info');
            const input = item.querySelector('input');
            if(itemInfo === selectedInfoId) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
                input.checked = false;
            }
        });
    });
});
</script>
@endsection
