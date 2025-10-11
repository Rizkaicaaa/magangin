@extends('layouts.app')

@section('title', 'Penilaian Wawancara | MagangIn')

@section('content')
<div class="bg-white p-8 rounded-xl shadow-lg mx-6">
    <div class="flex justify-between items-center mb-6">
        <h1 id="page-title" class="text-3xl font-bold text-gray-800">Kelola Penilaian Wawancara</h1>
        <a href="{{ route('penilaian-wawancara.create') }}" class="py-2 px-4 rounded-md bg-navy text-white font-semibold hover:bg-baby-blue transition-colors duration-300
            {{ $data->count() > 0 ? '' : 'hidden' }}">
            Tambah Penilaian
        </a>
    </div>

    {{-- Empty state --}}
    <div id="empty-state" class="text-center p-12 {{ count($data) > 0 ? 'hidden' : '' }}">
        <p class="text-gray-500 mb-4">
            Belum ada Penilaian Wawancara yang dimasukkan. Silakan buat penilaian baru dengan klik tombol di bawah.
        </p>
        <a href="{{ route('penilaian-wawancara.create') }}"
            class="py-2 px-4 rounded-md bg-navy text-white font-semibold hover:bg-baby-blue transition-colors duration-300">
            Tambah Penilaian
        </a>
    </div>

    {{-- Tabel data --}}
    <div id="data-state" class="{{ count($data) > 0 ? '' : 'hidden' }}">

        <table class="min-w-full bg-white border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-gray-100">
                <tr>
                    <th class="py-3 px-4 text-left text-gray-600 font-semibold">No</th>
                    <th class="py-3 px-4 text-left text-gray-600 font-semibold">Nama Peserta</th>
                    <th class="py-3 px-4 text-left text-gray-600 font-semibold">Nama Penilai</th>
                    <th class="py-3 px-4 text-left text-gray-600 font-semibold">Komunikasi</th>
                    <th class="py-3 px-4 text-left text-gray-600 font-semibold">Motivasi</th>
                    <th class="py-3 px-4 text-left text-gray-600 font-semibold">Kemampuan</th>
                    <th class="py-3 px-4 text-left text-gray-600 font-semibold">Nilai Total</th>
                    <th class="py-3 px-4 text-left text-gray-600 font-semibold">Nilai Rata-Rata</th>
                    <th class="py-3 px-4 text-left text-gray-600 font-semibold">Status</th>
                    <th class="py-3 px-4 text-left text-gray-600 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $item)
                <tr class="border-t">
                    <td class="py-3 px-4">{{ $loop->iteration }}</td>
                    <td class="py-3 px-4">{{ $item->pendaftaran?->user?->name ?? '-' }}</td>
                    <td class="px-4 py-2 border">{{ $item->pewawancara }}</td>
                    <td class="py-3 px-4">{{ $item->nilai_komunikasi }}</td>
                    <td class="py-3 px-4">{{ $item->nilai_motivasi }}</td>
                    <td class="py-3 px-4">{{ $item->nilai_kemampuan }}</td>
                    <td class="py-3 px-4">{{ $item->nilai_total }}</td>
                    <td class="py-3 px-4">{{ $item->nilai_rata_rata }}</td>
                    <td class="py-3 px-4">
                        @if($item->status === 'sudah_dinilai')
                        <span class="px-2 py-1 rounded-full bg-green-500 text-white text-sm">
                            Sudah Dinilai
                        </span>
                        @else
                        <span class="px-2 py-1 rounded-full bg-gray-400 text-white text-sm">
                            Belum Dinilai
                        </span>
                        @endif
                    </td>

                    <td class="py-3 px-4 flex gap-2">
                        {{-- Tombol Detail --}}
                        <a href="{{ route('penilaian-wawancara.show', $item->id) }}"
                            class="text-green-600 hover:text-green-800" title="Detail">
                            <!-- Icon Eye -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>

                        {{-- Tombol Edit --}}
                        <a href="{{ route('penilaian-wawancara.edit', $item->id) }}"
                            class="text-blue-600 hover:text-blue-800" title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.862 3.487a2.25 2.25 0 113.182 3.182L7.5 19.313 3 21l1.687-4.5L16.862 3.487z" />
                            </svg>
                        </a>

                        {{-- Tombol Hapus --}}
                        <form action="{{ route('penilaian-wawancara.destroy', $item->id) }}" method="POST"
                            class="inline delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="text-red-600 hover:text-red-800 delete-button" title="Hapus">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
// SweetAlert untuk tombol hapus
document.querySelectorAll('.delete-button').forEach(button => {
    button.addEventListener('click', function() {
        const form = this.closest('form');

        Swal.fire({
            title: 'Apakah kamu yakin?',
            text: "Data penilaian wawancara ini akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>

@if(session('success'))
<script>
Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: '{{ session('
    success ') }}',
    showConfirmButton: false,
    timer: 1500
});
</script>
@endif
@endsection