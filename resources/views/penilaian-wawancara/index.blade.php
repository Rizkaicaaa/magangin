@extends('layouts.app')

@section('title', 'Penilaian Wawancara | MagangIn')

@section('content')
<div class="bg-white p-8 rounded-xl shadow-lg mx-6 mt-4">
    <div class="flex flex-col sm:flex-row justify-between sm:items-center mb-6 gap-3">
        <h1 id="page-title" class="text-3xl font-bold text-gray-800">Kelola Penilaian Wawancara</h1>
    </div>

    {{-- Empty state --}}
    <div id="empty-state" class="text-center p-12 {{ count($data) > 0 ? 'hidden' : '' }}">
        <p class="text-gray-500 mb-4 text-lg">
            Belum ada Penilaian Wawancara yang dimasukkan.
        </p>
        <a href="{{ route('penilaian-wawancara.create') }}" 
           class="py-2 px-5 rounded-md bg-navy text-white font-semibold hover:bg-baby-blue transition duration-300 shadow-sm">
           + Tambah Penilaian
        </a>
    </div>

    {{-- Input KKM dan Tombol Tambah Penilaian --}}
    @if(count($data) > 0)
        <div class="flex justify-between items-center gap-3 mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm">
            <div class="flex items-center gap-3">
                <label for="kkm" class="font-semibold text-gray-700">Masukkan KKM:</label>
                <input type="number" step="0.01" id="kkm" name="kkm" 
                    value="{{ $kkm ?? '' }}" 
                    data-kkm="{{ $kkm ?? '' }}">

                <button id="btn-kkm" 
                    class="bg-navy text-white px-4 py-2 rounded-md font-semibold hover:bg-baby-blue transition duration-300">
                        Terapkan
                </button>

                <button id="btn-edit-kkm" 
                    class="bg-yellow-500 text-white px-4 py-2 rounded-md font-semibold hover:bg-yellow-600 transition duration-300 hidden">
                        Edit KKM
                </button>
            </div>

            <a href="{{ route('penilaian-wawancara.create') }}"
                class="py-2 px-5 rounded-md bg-navy text-white font-semibold hover:bg-baby-blue transition duration-300 shadow-sm">
                    + Tambah Penilaian
            </a>
        </div>
    @endif

    {{-- Tabel data --}}
    <div id="data-state" class="{{ count($data) > 0 ? '' : 'hidden' }}">
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden text-sm">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold">No</th>
                        <th class="py-3 px-4 text-left font-semibold">Nama Peserta</th>
                        <th class="py-3 px-4 text-left font-semibold">Nama Penilai</th>
                        <th class="py-3 px-4 text-left font-semibold">Komunikasi</th>
                        <th class="py-3 px-4 text-left font-semibold">Motivasi</th>
                        <th class="py-3 px-4 text-left font-semibold">Kemampuan</th>
                        <th class="py-3 px-4 text-left font-semibold">Nilai Total</th>
                        <th class="py-3 px-4 text-left font-semibold">Nilai Akhir</th>
                        <th class="py-3 px-4 text-left font-semibold">KKM</th>
                        <th class="py-3 px-4 text-left font-semibold">Status</th>
                        <th class="py-3 px-4 text-left font-semibold">Hasil Wawancara</th>
                        <th class="py-3 px-4 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($data as $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3 px-4">{{ $loop->iteration }}</td>
                        <td class="py-3 px-4 font-medium text-gray-800">{{ $item->nama_peserta }}</td>
                        <td class="py-3 px-4">{{ $item->pewawancara }}</td>
                        <td class="py-3 px-4">{{ $item->nilai_komunikasi }}</td>
                        <td class="py-3 px-4">{{ $item->nilai_motivasi }}</td>
                        <td class="py-3 px-4">{{ $item->nilai_kemampuan }}</td>
                        <td class="py-3 px-4">{{ $item->nilai_total }}</td>
                        <td class="py-3 px-4">{{ $item->nilai_rata_rata }}</td>
                        <td class="py-3 px-4 kkm-cell text-center">{{ $item->kkm ?? '-' }}</td>
                        <td class="py-3 px-4 text-center">
                            @if($item->status === 'sudah_dinilai')
                                <span class="px-2 py-1 rounded-full bg-green-500 text-white text-xs font-semibold">
                                    Sudah Dinilai
                                </span>
                            @else
                                <span class="px-2 py-1 rounded-full bg-gray-400 text-white text-xs font-semibold">
                                    Belum Dinilai
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 hasil-cell text-center">
                            @if(!is_null($item->nilai_rata_rata) && !is_null($item->kkm))
                                @if($item->nilai_rata_rata >= $item->kkm)
                                    <span class="px-2 py-1 rounded-full bg-green-500 text-white text-sm">Lulus Wawancara</span>
                                @else
                                    <span class="px-2 py-1 rounded-full bg-red-500 text-white text-sm">Tidak Lulus Wawancara</span>
                                @endif
                                @else
                                <span class="px-2 py-1 rounded-full bg-gray-400 text-white text-sm">Belum Dinilai</span>
                            @endif
                        </td>

                        <td class="py-3 px-4 text-center">
                            <div class="flex justify-center gap-3">
                                {{-- Detail --}}
                                <a href="{{ route('penilaian-wawancara.show', $item->id) }}" class="text-green-600 hover:text-green-800 transition" title="Detail">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>

                                {{-- Edit --}}
                                <a href="{{ route('penilaian-wawancara.edit', $item->id) }}" class="text-blue-600 hover:text-blue-800 transition" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.25 2.25 0 113.182 3.182L7.5 19.313 3 21l1.687-4.5L16.862 3.487z" />
                                    </svg>
                                </a>

                                {{-- Hapus --}}
                                <form action="{{ route('penilaian-wawancara.destroy', $item->id) }}" method="POST" class="inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="text-red-600 hover:text-red-800 transition delete-button" title="Hapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // === KONFIRMASI HAPUS DENGAN SWEETALERT ===
    document.querySelectorAll('.delete-button').forEach(button => {
        button.addEventListener('click', function () {
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
            }).then(result => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

document.addEventListener('DOMContentLoaded', function () {
    const inputKKM = document.getElementById('kkm');
    const btnTerapkan = document.getElementById('btn-kkm');
    const btnEdit = document.getElementById('btn-edit-kkm');
    const rows = document.querySelectorAll('tbody tr');

    // Fungsi untuk update tabel KKM & hasil wawancara
    function updateKKMTable(kkmValue) {
        rows.forEach(row => {
            const nilaiRataRata = parseFloat(row.children[7].innerText);
            const kkmCell = row.querySelector('.kkm-cell');
            const hasilCell = row.querySelector('.hasil-cell');

            kkmCell.textContent = Math.round(kkmValue);

            if (!isNaN(nilaiRataRata)) {
                hasilCell.innerHTML =
                    nilaiRataRata >= kkmValue
                        ? `<span class="px-2 py-1 rounded-full bg-green-500 text-white text-sm">Lulus Wawancara</span>`
                        : `<span class="px-2 py-1 rounded-full bg-red-500 text-white text-sm">Tidak Lulus Wawancara</span>`;
            } else {
                hasilCell.innerHTML = `<span class="px-2 py-1 rounded-full bg-gray-400 text-white text-sm">Belum Dinilai</span>`;
            }
        });
    }

    // Fungsi untuk set status tombol & input
    function setKKMStatus(kkmValue) {
        if (!isNaN(kkmValue) && kkmValue > 0) {
            inputKKM.value = kkmValue;
            inputKKM.disabled = true;
            btnTerapkan.disabled = true;
            btnTerapkan.classList.add('opacity-50', 'cursor-not-allowed');
            btnEdit.classList.remove('hidden');
        } else {
            inputKKM.disabled = false;
            btnTerapkan.disabled = false;
            btnTerapkan.classList.remove('opacity-50', 'cursor-not-allowed');
            btnEdit.classList.add('hidden');
        }
    }

    // Inisialisasi awal saat halaman load
    const firstRowKKM = rows[0]?.querySelector('.kkm-cell');
    const initialKKM = firstRowKKM ? parseFloat(firstRowKKM.textContent) : null;
    if (initialKKM) updateKKMTable(initialKKM);
    setKKMStatus(initialKKM);

    // Tombol Terapkan
    btnTerapkan.addEventListener('click', function () {
        const kkmValue = parseFloat(inputKKM.value);
        if (isNaN(kkmValue) || kkmValue <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Input tidak valid!',
                text: 'Masukkan nilai KKM yang benar (angka lebih dari 0).'
            });
            return;
        }

        // Kirim ke backend
        fetch("{{ route('penilaian-wawancara.updateStatus') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ kkm: kkmValue })
        })
        .then(response => response.json())
        .then(data => {
            Swal.fire({
                icon: 'success',
                title: 'KKM Ditetapkan!',
                text: data.message,
                showConfirmButton: false,
                timer: 2000
            });
            updateKKMTable(kkmValue);
            setKKMStatus(kkmValue);
        })
        .catch(error => {
            console.error(error);
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Terjadi kesalahan saat memperbarui status pendaftaran.'
            });
        });
    });

    // Tombol Edit KKM
    btnEdit.addEventListener('click', function () {
        inputKKM.disabled = false;
        btnTerapkan.disabled = false;
        btnTerapkan.classList.remove('opacity-50', 'cursor-not-allowed');
        btnEdit.classList.add('hidden');
        inputKKM.focus();
    });

    // SweetAlert untuk pesan sukses dari backend
    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 2000
        });
    @endif
});
</script>
@endsection
