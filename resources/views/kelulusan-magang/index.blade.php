@extends('layouts.app')

@section('title', 'Kelulusan Magang | MagangIn')

@section('content')
<div class="bg-white p-8 rounded-xl shadow-lg mx-6">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <h1 class="text-3xl font-bold text-gray-800">🎓 Kelulusan Magang</h1>
    </div>

    {{-- Jika tidak ada evaluasi --}}
    @if(!$evaluasi)
    <div class="text-center p-12">
        <p class="text-gray-500 mb-4">
            Belum ada hasil evaluasi magang untuk akun Anda.
        </p>
    </div>
    @else
    {{-- Tabel nilai --}}
    <div class="overflow-x-auto">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">
            Hasil evaluasi magang kamu dapat dilihat pada tabel berikut:
        </h2>
        <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="py-3 px-6 text-left">Aspek Penilaian</th>
                    <th class="py-3 px-6 text-left">Nilai</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <tr>
                    <td class="py-3 px-6">Kedisiplinan</td>
                    <td class="py-3 px-6">{{ $evaluasi->nilai_kedisiplinan ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="py-3 px-6">Kerjasama</td>
                    <td class="py-3 px-6">{{ $evaluasi->nilai_kerjasama ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="py-3 px-6">Inisiatif</td>
                    <td class="py-3 px-6">{{ $evaluasi->nilai_inisiatif ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="py-3 px-6">Hasil Kerja</td>
                    <td class="py-3 px-6">{{ $evaluasi->nilai_hasil_kerja ?? '-' }}</td>
                </tr>
                <tr class="bg-gray-50 font-semibold">
                    <td class="py-3 px-6">Total Nilai</td>
                    <td class="py-3 px-6">{{ $evaluasi->nilai_total ?? '-' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Status kelulusan (dari tabel pendaftaran) --}}
    @php
    $status = $evaluasi->pendaftaran->status_pendaftaran ?? null;
    @endphp

    <div class="mt-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Status Kelulusan</h2>

        @if($status === 'lulus_magang')
        <div class="p-6 rounded-lg border border-green-400 bg-green-50">
            <p class="text-lg font-bold text-green-700">
                Selamat, Kamu dinyatakan lulus magang pada periode ini 🎉
            </p>
        </div>
        @elseif($status === 'tidak_lulus_magang')
        <div class="p-6 rounded-lg border border-red-400 bg-red-50">
            <p class="text-lg font-bold text-red-700">
                Maaf, Kamu tidak lulus magang 😔
            </p>
        </div>
        @else
        <div class="p-6 rounded-lg border border-yellow-400 bg-yellow-50">
            <p class="text-lg font-semibold text-yellow-700">
                Status kelulusan Kamu belum ditentukan.
            </p>
        </div>
        @endif
    </div>

    {{-- Tombol Download Sertifikat --}}
    @if($evaluasi->file_sertifikat)
    <div class="mt-8">
        <a href="{{ asset('storage/' . $evaluasi->file_sertifikat) }}"
            class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition"
            target="_blank">
            <i class="fas fa-download mr-2"></i> Download Sertifikat
        </a>
    </div>
    @endif
    @endif
</div>
@endsection