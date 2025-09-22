@extends('layouts.app')

@section('title', 'Tambah Penilaian Wawancara | MagangIn')

@section('content')
<div class="bg-white p-8 rounded-xl shadow-lg mx-6">
    <h2 class="text-xl font-bold mb-4">Tambah Penilaian Wawancara</h2>
    <form action="{{ route('penilaian-wawancara.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700">Pilih Peserta</label>
            <select name="pendaftaran_id" class="w-full border rounded-lg p-2" required>
                <option value="" disabled selected>-- Pilih Peserta --</option>
                @foreach($peserta as $p)
                    <option value="{{ $p->id }}">{{ $p->user->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Pilih Penilai (Pewawancara)</label>
            <select name="jadwal_seleksi_id" class="w-full border rounded-lg p-2" required>
                <option value="" disabled selected>-- Pilih Pewawancara --</option>
                @foreach($jadwalseleksi as $j)
                <option value="{{ $j->id }}">{{ $j->pewawancara }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex gap-4">
            <div class="w-1/3">
                <label class="block text-sm font-medium text-gray-700">Nilai Komunikasi</label>
                <input type="number" name="nilai_komunikasi" min="0" max="100" 
                       class="w-full border rounded-lg p-2" placeholder="0-100">
            </div>
            <div class="w-1/3">
                <label class="block text-sm font-medium text-gray-700">Nilai Motivasi</label>
                <input type="number" name="nilai_motivasi" min="0" max="100" 
                       class="w-full border rounded-lg p-2" placeholder="0-100">
            </div>
            <div class="w-1/3">
                <label class="block text-sm font-medium text-gray-700">Nilai Kemampuan</label>
                <input type="number" name="nilai_kemampuan" min="0" max="100" 
                       class="w-full border rounded-lg p-2" placeholder="0-100">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" class="w-full border rounded-lg p-2" required>
                     <option value="" disabled selected>-- Pilih Status --</option>
                     <option value="belum_dinilai">Belum Dinilai</option>
                     <option value="sudah_dinilai">Sudah Dinilai</option>
                </select>
        </div>


        <div class="flex justify-end gap-2 mt-4">
            <a href="{{ route('penilaian-wawancara.index') }}" 
               class="px-4 py-2 rounded-md bg-gray-300 hover:bg-gray-400">Batal</a>
            <button type="reset" class="px-4 py-2 rounded-md bg-yellow-400 text-white hover:bg-yellow-500">Reset</button>
            <button type="submit" class="px-4 py-2 rounded-md bg-navy text-white hover:bg-baby-blue">Simpan</button>
        </div>
    </form>
</div>

@endsection
