@extends('layouts.app')

@section('title', 'Kelola Jadwal Wawancara | MagangIn')

@section('content')
<div class="bg-white p-8 rounded-2xl shadow-xl mx-6">
    <div class="flex justify-between items-center mb-6">
        <h1 id="page-title" class="text-3xl font-bold text-gray-800">Kelola Jadwal Wawancara</h1>
        <a href="{{ route('jadwal-seleksi.create') }}"
            class="py-2 px-4 rounded-xl bg-navy text-white font-semibold hover:bg-baby-blue transition duration-300">
            Tambah Jadwal
        </a>
    </div>

    <div id="content-container">
        {{-- Empty State --}}
        <div id="empty-state" class="text-center p-12 {{ count($jadwals) > 0 ? 'hidden' : '' }}">
            <p class="text-gray-500 text-lg mb-4">
                Belum ada Jadwal Wawancara yang dimasukkan.<br>
                Silakan buat jadwal baru dengan klik tombol di atas.
            </p>
        </div>

        {{-- Data State --}}
        <div id="data-state" class="{{ count($jadwals) > 0 ? '' : 'hidden' }}">
            <form action="{{ route('jadwal-seleksi.index') }}" method="GET" class="mb-6 flex gap-2">
                <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                    class="border border-gray-300 px-4 py-2 rounded-l-full focus:outline-none focus:ring-2 focus:ring-baby-blue">

                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari berdasarkan nama atau tempat"
                    class="border-t border-b border-gray-300 px-4 py-2 w-96 focus:outline-none focus:ring-2 focus:ring-baby-blue">

                <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded-r-full hover:bg-blue-700 transition duration-300">
                    Filter / Cari
                </button>
            </form>

            <div class="overflow-x-auto rounded-2xl shadow-sm">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead class="bg-gray-100 text-center">
                        <tr>
                            <th class="py-3 px-4 text-gray-600 font-semibold w-12">No</th>
                            <th class="py-3 px-4 text-gray-600 font-semibold w-48">Nama OR</th>
                            <th class="py-3 px-4 text-gray-600 font-semibold w-48">Nama Peserta</th>
                            <th class="py-3 px-4 text-gray-600 font-semibold w-36">Tanggal Wawancara</th>
                            <th class="py-3 px-4 text-gray-600 font-semibold w-40">Waktu Wawancara</th>
                            <th class="py-3 px-4 text-gray-600 font-semibold w-48">Tempat</th>
                            <th class="py-3 px-4 text-gray-600 font-semibold w-40">Pewawancara</th>
                            <th class="py-3 px-4 text-gray-600 font-semibold w-36">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($jadwals as $jadwal)
                        <tr class="border-t hover:bg-gray-50 transition duration-200">
                            <td class="py-3 px-4 text-center">{{ $loop->iteration }}</td>
                            <td class="py-3 px-4 text-left">{{ $jadwal->infoOr->judul ?? '-' }}</td>
                            <td class="py-3 px-4 text-left">{{ $jadwal->pendaftaran->user->nama_lengkap ?? '-' }}</td>
                            <td class="py-3 px-4 text-center">{{ \Carbon\Carbon::parse($jadwal->tanggal_seleksi)->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-center font-mono text-sm">
                                {{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }} WIB
                            </td>
                            <td class="py-3 px-4 text-left">{{ $jadwal->tempat ?? '-' }}</td>
                            <td class="py-3 px-4 text-left">{{ $jadwal->pewawancara ?? '-' }}</td>
                            <td class="py-3 px-4 flex gap-3 justify-center">
                                {{-- Detail --}}
                                <a href="{{ route('jadwal-seleksi.show', $jadwal->id) }}"
                                    class="text-green-600 hover:text-green-800 transition" title="Detail">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>

                                {{-- Edit --}}
                                <a href="{{ route('jadwal-seleksi.edit', $jadwal->id) }}"
                                    class="text-blue-600 hover:text-blue-800 transition" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.862 3.487a2.25 2.25 0 113.182 3.182L7.5 19.313 3 21l1.687-4.5L16.862 3.487z" />
                                    </svg>
                                </a>

                                {{-- Hapus --}}
                                <form action="{{ route('jadwal-seleksi.destroy', $jadwal->id) }}" method="POST"
                                    class="inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="text-red-600 hover:text-red-800 delete-button transition"
                                        title="Hapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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
            
            <div class="mt-4">
                {{ $jadwals->appends(['search' => request('search')])->links() }}
            </div>
        </div>
    </div>
</div>

<script>
// SweetAlert untuk tombol hapus
document.querySelectorAll('.delete-button').forEach(button => {
    button.addEventListener('click', function() {
        const form = this.closest('form');

        Swal.fire({
            title: 'Apakah kamu yakin?',
            text: "Data jadwal wawancara ini akan dihapus!",
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
    text: '{{ session('success') }}',
    showConfirmButton: false,
    timer: 1500
});
</script>
@endif
@endsection
