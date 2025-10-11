@extends('layouts.app')

@section('title', 'Pengumuman Kelulusan | MagangIn')

@section('content')
<div class="bg-white p-8 rounded-xl shadow-lg max-w-3xl mx-auto mt-8 border border-gray-200">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Buat Pengumuman Kelulusan Magang</h1>
    </div>

    {{-- Pesan sukses / error --}}
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
    <form method="POST" id="form-pengumuman" class="space-y-6">
        @csrf

        {{-- Pilih Mahasiswa --}}
        <div>
            <label for="evaluasi_id" class="block text-sm font-semibold text-gray-700 mb-2">
                Pilih Mahasiswa
            </label>
            <select id="evaluasi_id" name="evaluasi_id" required
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-baby-blue focus:border-baby-blue transition">
                <option value="" disabled selected>-- Pilih Mahasiswa --</option>
                @foreach ($evaluasiList as $evaluasi)
                <option value="{{ $evaluasi->id }}">
                    {{ $evaluasi->pendaftaran->user->nama_lengkap }}
                </option>
                @endforeach
            </select>
        </div>

        {{-- Pilih Template Sertifikat --}}
        <div>
            <label for="template_id" class="block text-sm font-semibold text-gray-700 mb-2">
                Pilih Template Sertifikat
            </label>
            <select id="template_id" name="template_id" required
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-baby-blue focus:border-baby-blue transition">
                <option value="" disabled selected>-- Pilih Template (terbaru di atas) --</option>
                @foreach ($templates as $template)
                <option value="{{ $template->id }}">{{ $template->nama_template }}</option>
                @endforeach
            </select>
        </div>

        {{-- Nomor Sertifikat --}}
        <div>
            <label for="nomor_sertifikat" class="block text-sm font-semibold text-gray-700 mb-2">
                Nomor Sertifikat (contoh: M/2025/)
            </label>
            <input type="text" name="nomor_sertifikat" id="nomor_sertifikat" required
                placeholder="Masukkan awalan nomor sertifikat"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-baby-blue focus:border-baby-blue px-3 py-2 transition">
        </div>

        {{-- Tombol Kembali & Submit --}}
        <div class="flex justify-end items-center space-x-3">
            <a href="{{ url('/penilaian') }}"
                class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition-all duration-300 shadow-sm">
                Kembali
            </a>

            <button type="submit"
                class="px-5 py-2.5 bg-navy text-white rounded-lg font-semibold hover:bg-baby-blue transition-all duration-300 shadow-sm">
                Simpan
            </button>
        </div>
    </form>
</div>

{{-- Script ubah action form --}}
<script>
document.getElementById('form-pengumuman').addEventListener('submit', function(e) {
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