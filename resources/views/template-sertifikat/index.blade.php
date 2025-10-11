@extends('layouts.app')

@section('title', 'Upload Template Sertifikat | MagangIn')

@section('content')
<div class="bg-white rounded-xl shadow-md p-8 max-w-6xl mx-auto mt-8 border border-gray-100">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-navy mb-6">Upload Template Sertifikat Magang</h1>

    </div>

    @if (session('success'))
    <div class="mb-6 p-4 rounded-lg bg-green-100 text-green-700 font-medium border border-green-200">
        {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('template.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Pilih Info OR --}}
        <div>
            <label for="info_or_id" class="block text-sm font-semibold text-gray-700 mb-2">
                Pilih Info OR Aktif
            </label>
            <select name="info_or_id" id="info_or_id" required
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-baby-blue focus:border-baby-blue transition">
                <option value="">Pilih Info OR</option>
                @forelse ($infoOrList as $info)
                <option value="{{ $info->id }}">{{ $info->judul }}</option>
                @empty
                <option disabled>Belum ada info OR yang aktif</option>
                @endforelse
            </select>
            @error('info_or_id')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Nama Template --}}
        <div>
            <label for="nama_template" class="block text-sm font-semibold text-gray-700 mb-2">
                Nama Template Sertifikat
            </label>
            <input type="text" name="nama_template" id="nama_template"
                placeholder="Contoh: Sertifikat Magang Periode Oktober 2025"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-baby-blue focus:border-baby-blue px-3 py-2"
                required>
            @error('nama_template')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Upload File Template --}}
        <div>
            <label for="file_template" class="block text-sm font-semibold text-gray-700 mb-2">
                Upload File Template
            </label>
            <input type="file" name="file_template" id="file_template" accept=".html"
                class="w-full border border-gray-300 rounded-md px-3 py-2 shadow-sm focus:ring-baby-blue focus:border-baby-blue transition"
                required>
            <p class="text-sm text-gray-500 mt-2">
                Format yang diterima: <span class="font-medium text-navy"> .html</span> (maks 2MB)
            </p>
            @error('file_template')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end items-center space-x-3">
            {{-- Tombol Kembali --}}
            <a href="{{ url('/penilaian') }}"
                class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition-all duration-300 shadow-sm">
                Kembali
            </a>

            {{-- Tombol Submit --}}
            <button type="submit"
                class="px-5 py-2.5 bg-navy text-white rounded-lg font-semibold hover:bg-baby-blue transition-all duration-300 shadow-sm">
                Upload Template
            </button>
        </div>

    </form>
</div>
@endsection