{{-- resources/views/hasilwawancara/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="bg-white p-8 rounded-xl shadow-lg mx-6">
    <div class="flex justify-between items-center mb-6">
        <h1 id="page-title" class="text-3xl font-bold text-gray-800">Pengumuman Hasil Wawancara</h1>

        {{-- Tombol buat kalau ada data --}}
        <a href="{{ route('hasilwawancara.create') }}" 
           id="create-button"
           class="py-2 px-4 rounded-md bg-navy text-white font-semibold hover:bg-baby-blue transition-colors duration-300 {{ count($hasilSeleksi) > 0 ? '' : 'hidden' }}">
            Tambah Hasil Wawancara
        </a>
    </div>

    <div id="content-container">
        {{-- Kalau kosong --}}
        <div id="empty-state" class="text-center p-12 {{ count($hasilSeleksi) > 0 ? 'hidden' : '' }}">
            <p class="text-gray-500 mb-4">
                Belum ada hasil wawancara yang dimasukkan. Silakan tambah data dengan klik tombol di bawah.
            </p>
            <a href="{{ route('hasilwawancara.create') }}" 
               id="empty-create-button" 
               class="py-2 px-4 rounded-md bg-navy text-white font-semibold hover:bg-baby-blue transition-colors duration-300">
                Tambah Hasil Wawancara
            </a>
        </div>

        {{-- Kalau ada data --}}
        <div id="data-state" class="{{ count($hasilSeleksi) > 0 ? '' : 'hidden' }}">
            <table class="w-full border border-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-4 py-2 border">ID Hasil Seleksi</th>
                        <th class="px-4 py-2 border">ID Nilai Wawancara</th>
                        <th class="px-4 py-2 border">Nilai Total</th>
                        <th class="px-4 py-2 border">Status Seleksi</th>
                        <th class="px-4 py-2 border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($hasilSeleksi as $hasil)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-2 border text-center">{{ $hasil->ID_Hasil_Seleksi }}</td>
                            <td class="px-4 py-2 border text-center">{{ $hasil->ID_Nilai_Wawancara }}</td>
                            <td class="px-4 py-2 border text-center">{{ $hasil->Nilai_Total }}</td>
                            <td class="px-4 py-2 border text-center">
                                @if($hasil->Status_Seleksi == 'Lulus')
                                    <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 font-semibold">Lulus</span>
                                @elseif($hasil->Status_Seleksi == 'Tidak Lulus')
                                    <span class="px-3 py-1 rounded-full bg-red-100 text-red-700 font-semibold">Tidak Lulus</span>
                                @else
                                    <span class="px-3 py-1 rounded-full bg-gray-200 text-gray-700 font-semibold">{{ $hasil->Status_Seleksi }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 border text-center">
                                {{-- Button Edit --}}
                                <a href="{{ route('hasilwawancara.edit', $hasil->ID_Hasil_Seleksi) }}"
                                   class="px-3 py-1 rounded-md bg-yellow-500 text-white font-semibold hover:bg-yellow-600 transition-colors duration-300">
                                    Edit
                                </a>
                                {{-- Button Hapus --}}
                                <form action="{{ route('hasilwawancara.destroy', $hasil->ID_Hasil_Seleksi) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Yakin ingin menghapus data ini?')"
                                            class="px-3 py-1 rounded-md bg-red-600 text-white font-semibold hover:bg-red-700 transition-colors duration-300">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
