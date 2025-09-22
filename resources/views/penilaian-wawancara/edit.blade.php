@extends('layouts.app')

@section('title', 'Edit Penilaian Wawancara | MagangIn')

@section('content')
<div class="bg-white p-8 rounded-xl shadow-lg mx-6">
    <h2 class="text-xl font-bold mb-4">Edit Penilaian Wawancara</h2>

    <form action="{{ route('penilaian-wawancara.update', $penilaianWawancara->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700">Pilih Peserta</label>
            <select name="pendaftaran_id" class="w-full border rounded-lg p-2" required>
                @foreach($peserta as $p)
                    <option value="{{ $p->id }}" {{ $p->id == $penilaianWawancara->pendaftaran_id ? 'selected' : '' }}>
                        {{ $p->user->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Pilih Penilai (Pewawancara)</label>
            <select name="jadwal_seleksi_id" class="w-full border rounded-lg p-2" required>
                @foreach($jadwalseleksi as $j)
                    <option value="{{ $j->id }}" {{ $j->id == $penilaianWawancara->jadwal_seleksi_id ? 'selected' : '' }}>
                        {{ $j->pewawancara }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex gap-4">
            <div class="w-1/3">
                <label class="block text-sm font-medium text-gray-700">Nilai Komunikasi</label>
                <input type="number" name="nilai_komunikasi" min="0" max="100" 
                       value="{{ $penilaianWawancara->nilai_komunikasi }}" 
                       class="w-full border rounded-lg p-2">
            </div>
            <div class="w-1/3">
                <label class="block text-sm font-medium text-gray-700">Nilai Motivasi</label>
                <input type="number" name="nilai_motivasi" min="0" max="100" 
                       value="{{ $penilaianWawancara->nilai_motivasi }}" 
                       class="w-full border rounded-lg p-2">
            </div>
            <div class="w-1/3">
                <label class="block text-sm font-medium text-gray-700">Nilai Kemampuan</label>
                <input type="number" name="nilai_kemampuan" min="0" max="100" 
                       value="{{ $penilaianWawancara->nilai_kemampuan }}" 
                       class="w-full border rounded-lg p-2">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Status</label>
            <select name="status" class="w-full border rounded-lg p-2" required>
                <option value="belum_dinilai" {{ $penilaianWawancara->status == 'belum_dinilai' ? 'selected' : '' }}>Belum Dinilai</option>
                <option value="sudah_dinilai" {{ $penilaianWawancara->status == 'sudah_dinilai' ? 'selected' : '' }}>Sudah Dinilai</option>
            </select>
        </div>

        <div class="flex justify-end gap-2 mt-4">
            <a href="{{ route('penilaian-wawancara.index') }}" 
               class="px-4 py-2 rounded-md bg-gray-300 hover:bg-gray-400">Batal</a>
            <button type="submit" class="px-4 py-2 rounded-md bg-navy text-white hover:bg-baby-blue">
                Update
            </button>
        </div>
    </form>
</div>
@endsection
