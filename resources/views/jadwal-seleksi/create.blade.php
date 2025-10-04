@extends('layouts.app')

@section('title', 'Tambah Jadwal Wawancara | MagangIn')

@section('content')
<div class="bg-white p-8 rounded-xl shadow-lg mx-6">
    <h2 class="text-xl font-bold mb-4">Tambah Jadwal Wawancara</h2>

    <form action="{{ route('jadwal-seleksi.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700">Pilih Info OR</label>
            <select name="info_or_id" class="w-full border rounded-lg p-2" required>
                <option value="" disabled selected>-- Pilih Info OR --</option>
                @foreach ($infos as $info)
                    <option value="{{ $info->id }}">{{ $info->judul }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Tanggal Seleksi</label>
            <input type="date" name="tanggal_seleksi" 
                   class="w-full border rounded-lg p-2" 
                   placeholder="Pilih tanggal seleksi"
                   required>
        </div>

        <div class="flex gap-4">
            <div class="w-1/2">
                <label class="block text-sm font-medium text-gray-700">Waktu Mulai</label>
                <input type="time" name="waktu_mulai" 
                       class="w-full border rounded-lg p-2" 
                       placeholder="Waktu mulai"
                       required>
            </div>
            <div class="w-1/2">
                <label class="block text-sm font-medium text-gray-700">Waktu Selesai</label>
                <input type="time" name="waktu_selesai" 
                       class="w-full border rounded-lg p-2" 
                       placeholder="Waktu selesai"
                       required>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Tempat</label>
            <input type="text" name="tempat" 
                   class="w-full border rounded-lg p-2" 
                   placeholder="Masukkan lokasi wawancara"
                   required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Nama Pewawancara</label>
            <input type="text" name="pewawancara" 
                   class="w-full border rounded-lg p-2" 
                   placeholder="Masukkan nama pewawancara"
                   required>
        </div>

        <div class="flex justify-end gap-2 mt-4">
            <a href="{{ route('jadwal-seleksi.index') }}" 
               class="px-4 py-2 rounded-md bg-gray-300 hover:bg-gray-400">Batal</a>
                <button type="reset" 
                    class="px-4 py-2 rounded-md bg-yellow-400 text-white hover:bg-yellow-500">
                Reset
            </button>
            <button type="submit" class="px-4 py-2 rounded-md bg-navy text-white hover:bg-baby-blue">
                Simpan
            </button>
        </div>
        
    </form>
</div>
@endsection
