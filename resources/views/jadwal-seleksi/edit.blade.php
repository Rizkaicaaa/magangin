@extends('layouts.app')

@section('title', 'Edit Jadwal Wawancara | MagangIn')

@section('content')
<div class="bg-white p-8 rounded-xl shadow-lg mx-6">
    <h2 class="text-xl font-bold mb-4">Edit Jadwal Wawancara</h2>

    <form action="{{ route('jadwal-seleksi.update', $jadwalSeleksi->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        {{-- Pilih Info OR --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Pilih Info OR</label>
            <select name="info_or_id" class="w-full border rounded-lg p-2" required>
                <option value="" disabled>-- Pilih Info OR --</option>
                @foreach ($infos as $info)
                <option value="{{ $info->id }}" {{ $jadwalSeleksi->info_or_id == $info->id ? 'selected' : '' }}>
                    {{ $info->judul }}
                </option>
                @endforeach
            </select>
        </div>

        {{-- Tanggal dan Waktu --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Tanggal Seleksi</label>
            <input type="date" name="tanggal_seleksi"
                value="{{ \Carbon\Carbon::parse($jadwalSeleksi->tanggal_seleksi)->format('Y-m-d') }}"
                class="w-full border rounded-lg p-2" required>
        </div>

        <div class="flex gap-4">
            <div class="w-1/2">
                <label class="block text-sm font-medium text-gray-700">Waktu Mulai</label>
                <input type="time" name="waktu_mulai"
                    value="{{ \Carbon\Carbon::parse($jadwalSeleksi->waktu_mulai)->format('H:i') }}"
                    class="w-full border rounded-lg p-2" required>
            </div>
            <div class="w-1/2">
                <label class="block text-sm font-medium text-gray-700">Waktu Selesai</label>
                <input type="time" name="waktu_selesai"
                    value="{{ \Carbon\Carbon::parse($jadwalSeleksi->waktu_selesai)->format('H:i') }}"
                    class="w-full border rounded-lg p-2" required>
            </div>
        </div>

        {{-- Tempat dan Pewawancara --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Tempat</label>
            <input type="text" name="tempat" value="{{ $jadwalSeleksi->tempat }}" class="w-full border rounded-lg p-2"
                required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Nama Pewawancara</label>
            <input type="text" name="pewawancara" value="{{ $jadwalSeleksi->pewawancara }}"
                class="w-full border rounded-lg p-2" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Pilih Peserta Wawancara</label>
            <div id="peserta-container" class="border rounded-lg p-3 max-h-64 overflow-y-auto">
                @foreach($pendaftarans as $p)
                <div class="flex items-center mb-2 peserta-item" data-info="{{ $p->info_or_id }}">
                    <input type="radio" name="pendaftaran_id" value="{{ $p->id }}" id="p{{ $p->id }}" class="mr-2"
                        {{ (old('pendaftaran_id') ?? $jadwalSeleksi->pendaftaran_id) == $p->id ? 'checked' : '' }}>
                    <label for="p{{ $p->id }}">
                        {{ $p->user->nama_lengkap ?? 'Nama tidak tersedia' }}
                        — <span class="text-gray-500 text-sm">{{ $p->infoOr->judul ?? '-' }}</span>
                    </label>
                </div>
                @endforeach

                <p id="no-peserta" class="text-gray-500 p-2" style="display:none">Tidak ada peserta untuk Info OR ini.
                </p>
            </div>
        </div>



        {{-- Tombol --}}
        <div class="flex justify-end gap-2 mt-4">
            <a href="{{ route('jadwal-seleksi.index') }}"
                class="px-4 py-2 rounded-md bg-gray-300 hover:bg-gray-400">Batal</a>
            <button type="submit" class="px-4 py-2 rounded-md bg-navy text-white hover:bg-baby-blue">
                Update
            </button>
        </div>
    </form>
</div>

{{-- === SCRIPT FILTER PESERTA BERDASARKAN INFO OR === --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const infoSelect = document.querySelector('select[name="info_or_id"]');
    const pesertaItems = document.querySelectorAll('.peserta-item');
    const selectedInfo = infoSelect.value;

    // Saat halaman pertama kali dimuat, tampilkan hanya peserta dari info_or_id yang sama
    pesertaItems.forEach(item => {
        const itemInfo = item.getAttribute('data-info');
        const checkbox = item.querySelector('input');

        if (itemInfo === selectedInfo) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
            checkbox.checked = false;
        }
    });

    // Saat user mengganti Info OR
    infoSelect.addEventListener('change', function() {
        const selectedInfoId = this.value;

        pesertaItems.forEach(item => {
            const itemInfo = item.getAttribute('data-info');
            const checkbox = item.querySelector('input');

            if (itemInfo === selectedInfoId) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
                checkbox.checked = false;
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const infoSelect = document.querySelector('select[name="info_or_id"]');
    const pesertaItems = document.querySelectorAll('.peserta-item');

    // Ambil id peserta yang sudah dipilih sebelumnya
    const selectedPeserta = document.querySelector('input[name="pendaftaran_id"]:checked');
    const selectedPesertaInfo = selectedPeserta ? selectedPeserta.closest('.peserta-item').dataset.info : null;

    function filterPeserta(selectedInfoId) {
        let visibleCount = 0;
        pesertaItems.forEach(item => {
            const itemInfo = item.dataset.info;
            const input = item.querySelector('input');

            // Tampilkan jika info OR cocok atau dia adalah peserta yang sudah dipilih
            if (itemInfo === selectedInfoId || (selectedPeserta && input.value === selectedPeserta
                    .value)) {
                item.style.display = 'flex';
                visibleCount++;
            } else {
                item.style.display = 'none';
                input.checked = false;
            }
        });

        // Pesan "tidak ada peserta"
        document.getElementById('no-peserta').style.display = visibleCount === 0 ? 'block' : 'none';
    }

    // Saat halaman pertama kali dimuat
    filterPeserta(infoSelect.value);

    // Saat user mengganti Info OR
    infoSelect.addEventListener('change', function() {
        filterPeserta(this.value);
    });
});
</script>
@endsection