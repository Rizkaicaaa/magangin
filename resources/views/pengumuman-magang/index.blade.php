@extends('layouts.app')

@section('title', 'Pengumuman Kelulusan | MagangIn')

@section('content')
<div class="bg-white rounded-xl shadow-md p-8 max-w-6xl mx-auto mt-8 border border-gray-100">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-navy">Buat Pengumuman Kelulusan Magang</h1>

        <div class="flex space-x-3">
            <button id="create-button"
                class="py-2 px-4 rounded-md bg-navy text-white font-semibold hover:bg-baby-blue transition-colors duration-300">
                Buat Penilaian
            </button>

            <a href="{{ route('template.upload') }}"
                class="py-2 px-4 rounded-md bg-navy text-white font-semibold hover:bg-baby-blue transition-colors duration-300">
                Upload Template Sertifikat
            </a>
        </div>
    </div>

    {{-- Pesan sukses atau error --}}
    @if (session('success'))
        <div class="mb-6 p-4 rounded-lg bg-green-100 text-green-700 font-medium border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 rounded-lg bg-red-100 text-red-700 font-medium border border-red-200">
            {{ session('error') }}
        </div>
    @endif

    {{-- Form Buat Pengumuman --}}
    <form method="POST" id="form-pengumuman">
        @csrf

        {{-- Pilih Mahasiswa --}}
        <div class="mb-4">
            <label for="evaluasi_id" class="block text-gray-700 font-semibold mb-2">
                Pilih Mahasiswa
            </label>
            <select id="evaluasi_id" name="evaluasi_id"
                class="w-full border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500" required>
                <option value="" disabled selected>-- Pilih Mahasiswa --</option>
                @foreach ($evaluasiList as $evaluasi)
                    <option value="{{ $evaluasi->id }}">
                        {{ $evaluasi->pendaftaran->user->nama_lengkap }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Pilih Template Sertifikat --}}
        <div class="mb-4">
            <label for="template_id" class="block text-gray-700 font-semibold mb-2">
                Pilih Template Sertifikat
            </label>
            <select id="template_id" name="template_id"
                class="w-full border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500" required>
                <option value="" disabled selected>-- Pilih Template (terbaru di atas) --</option>
                @foreach ($templates as $template)
                    <option value="{{ $template->id }}">{{ $template->nama_template }}</option>
                @endforeach
            </select>
        </div>

        {{-- Input Nomor Sertifikat --}}
        <div class="mb-6">
            <label for="nomor_sertifikat" class="block text-gray-700 font-semibold mb-2">
                Nomor Sertifikat (contoh: M/2025/)
            </label>
            <input type="text" name="nomor_sertifikat" id="nomor_sertifikat"
                class="w-full border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Masukkan awalan nomor sertifikat" required>
        </div>

        <button type="submit"
            class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white px-6 py-2 rounded-lg font-semibold hover:scale-105 transition">
            Simpan Sertifikat
        </button>
    </form>
</div>

{{-- Script ubah action form --}}
<script>
document.getElementById('form-pengumuman').addEventListener('submit', function (e) {
    e.preventDefault();
    const evaluasiId = document.getElementById('evaluasi_id').value;
    if (!evaluasiId) {
        alert('Pilih mahasiswa terlebih dahulu!');
        return;
    }
    this.action = `/pengumuman/${evaluasiId}/store`;
    this.submit();
});
</script>
@endsection
