@extends('layouts.app')

@section('title', 'Kelola Jadwal Wawancara | MagangIn')

@section('content')
<div class="bg-white p-8 rounded-xl shadow-lg mx-6">
    <div class="flex justify-between items-center mb-6">
        <h1 id="page-title" class="text-3xl font-bold text-gray-800">Kelola Jadwal Wawancara</h1>
        <button id="create-button" 
            class="py-2 px-4 rounded-md bg-navy text-white font-semibold hover:bg-baby-blue transition-colors duration-300 
            {{ $jadwals->count() > 0 ? '' : 'hidden' }}">
            Tambah Jadwal
        </button>
    </div>

    <div id="content-container">
        {{-- Kondisi kalau data masih kosong --}}
        <div id="empty-state" class="text-center p-12 {{ count($jadwals) > 0 ? 'hidden' : '' }}">
            <p class="text-gray-500 mb-4">
                Belum ada Jadwal Wawancara yang dimasukkan. Silakan buat jadwal baru dengan klik tombol di bawah
            </p>
            <button id="empty-create-button" 
                class="py-2 px-4 rounded-md bg-navy text-white font-semibold hover:bg-baby-blue transition-colors duration-300">
                Tambah Jadwal
            </button>
        </div>

        {{-- Kalau ada data, tampilkan tabel --}}
        <div id="data-state" class="{{ count($jadwals) > 0 ? '' : 'hidden' }}">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-3 px-4 text-left text-gray-600 font-semibold">Tanggal</th>
                        <th class="py-3 px-4 text-left text-gray-600 font-semibold">Waktu</th>
                        <th class="py-3 px-4 text-left text-gray-600 font-semibold">Lokasi</th>
                        <th class="py-3 px-4 text-left text-gray-600 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($jadwals as $jadwal)
                        <tr class="border-t">
                            <td class="py-3 px-4">{{ $jadwal->tanggal_seleksi }}</td>
                            <td class="py-3 px-4">{{ $jadwal->waktu_mulai }} - {{ $jadwal->waktu_selesai }}</td>
                            <td class="py-3 px-4">{{ $jadwal->tempat }}</td>
                            <td class="py-3 px-4">
                                <button class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">Edit</button>
                                <button class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">Hapus</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Tambah Jadwal --}}
<div id="create-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6 relative">
        <h2 class="text-xl font-bold mb-4">Tambah Jadwal</h2>
        <form action="{{ route('jadwal-seleksi.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Pilih Info OR</label>
                <select name="info_or_id" class="w-full border rounded-lg p-2">
                    @foreach ($infos as $info)
                        <option value="{{ $info->id }}">{{ $info->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Tanggal Seleksi</label>
                <input type="date" name="tanggal_seleksi" class="w-full border rounded-lg p-2" required>
            </div>
            <div class="flex gap-4">
                <div class="w-1/2">
                    <label class="block text-sm font-medium text-gray-700">Waktu Mulai</label>
                    <input type="time" name="waktu_mulai" class="w-full border rounded-lg p-2" required>
                </div>
                <div class="w-1/2">
                    <label class="block text-sm font-medium text-gray-700">Waktu Selesai</label>
                    <input type="time" name="waktu_selesai" class="w-full border rounded-lg p-2" required>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Tempat</label>
                <input type="text" name="tempat" class="w-full border rounded-lg p-2" required>
            </div>
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" id="cancel-button" class="px-4 py-2 rounded-md bg-gray-300 hover:bg-gray-400">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-md bg-navy text-white hover:bg-baby-blue">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Script Modal --}}
<script>
    const modal = document.getElementById('create-modal');
    const openButtons = [document.getElementById('create-button'), document.getElementById('empty-create-button')];
    const cancelButton = document.getElementById('cancel-button');

    openButtons.forEach(btn => {
        if (btn) {
            btn.addEventListener('click', () => {
                modal.classList.remove('hidden');
            });
        }
    });

    cancelButton.addEventListener('click', () => {
        modal.classList.add('hidden');
    });
</script>
@endsection