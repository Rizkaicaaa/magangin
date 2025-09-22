@extends('layouts.app')

@section('content')
<div class="bg-white p-8 rounded-xl shadow-lg mx-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Hasil Wawancara</h1>
        <a href="#"
           class="py-2 px-4 rounded-md bg-navy text-white font-semibold hover:bg-baby-blue transition-colors duration-300">
           Tambah Hasil
        </a>
    </div>

    <div id="content-container">
        <table class="w-full border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="py-3 px-4">No</th>
                    <th class="py-3 px-4">Nama Pendaftar</th>
                    <th class="py-3 px-4">Nilai Total</th>
                    <th class="py-3 px-4">Status Seleksi</th>
                    <th class="py-3 px-4">Dinas Diterima</th>
                    <th class="py-3 px-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $dummyData = [
                        ['nama' => 'Budi', 'nilai_total' => 80, 'dinas' => 'Dinas A'],
                        ['nama' => 'Siti', 'nilai_total' => 70, 'dinas' => 'Dinas B'],
                        ['nama' => 'Putri', 'nilai_total' => 90, 'dinas' => 'Dinas C'],
                    ];
                @endphp

                @foreach($dummyData as $index => $item)
                <tr class="border-t">
                    <td class="py-3 px-4">{{ $index + 1 }}</td>
                    <td class="py-3 px-4">{{ $item['nama'] }}</td>
                    <td class="py-3 px-4">{{ $item['nilai_total'] }}</td>
                    <td class="py-3 px-4">
                        @if($item['nilai_total'] >= 75)
                            <span class="text-green-600 font-semibold">Lolos</span>
                        @else
                            <span class="text-red-600 font-semibold">Tidak Lolos</span>
                        @endif
                    </td>
                    <td class="py-3 px-4">{{ $item['dinas'] }}</td>
                    <td class="py-3 px-4 flex space-x-2">
                        <a href="#"
                           class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-md text-sm">
                           Edit
                        </a>
                        <button onclick="alert('Hapus data dummy!')" 
                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md text-sm">
                            Hapus
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

