@extends('layouts.app')

@section('title', 'Kelola Penilaian | MagangIn')

@section('content')

<style>
    table th, table td {
        text-align: center;
        vertical-align: middle;
        padding: 0.75rem;
    }

    table tbody tr:hover {
        background-color: #f9fafb;
    }
</style>

<div class="bg-white p-8 rounded-xl shadow-lg mx-6">
    <div class="flex justify-between items-center mb-6">
        <h1 id="page-title" class="text-3xl font-bold text-gray-800">Kelola Penilaian Mahasiswa Magang</h1>
        {{-- Tombol Buat Penilaian baru (hanya muncul kalau data ada) --}}
        @if ($penilaian->isNotEmpty())
            <button id="create-button" class="py-2 px-4 rounded-md bg-navy text-white font-semibold hover:bg-baby-blue transition-colors duration-300">
                Buat Penilaian
            </button>
        @endif
    </div>

    {{-- Jika belum ada data --}}
    @if ($penilaian->isEmpty())
        <div id="empty-state" class="text-center p-12">
            <p class="text-gray-500 mb-4">
                Belum ada Data yang dimasukkan. Silakan klik tombol buat di bawah untuk membuat Penilaian Magang
            </p>
            <button id="empty-create-button" class="py-2 px-4 rounded-md bg-navy text-white font-semibold hover:bg-baby-blue transition-colors duration-300">
                Buat Penilaian
            </button>
        </div>
    @endif

    {{-- Tabel Data --}}
    <div id="table-state" class="{{ $penilaian->isEmpty() ? 'hidden' : '' }} overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th>No.</th>
                    <th>Nama Peserta</th>
                    <th>Kedisiplinan</th>
                    <th>Inisiatif</th>
                    <th>Kerjasama</th>
                    <th>Kinerja</th>
                    <th>Total Nilai</th>
                    <th>KKM</th>
                    <th>Status Magang</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($penilaian as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->pendaftaran->user->nama_lengkap ?? '-' }}</td>
                        <td>{{ intval($item->nilai_kedisiplinan) }}</td>
                        <td>{{ intval($item->nilai_inisiatif) }}</td>
                        <td>{{ intval($item->nilai_kerjasama) }}</td>
                        <td>{{ intval($item->nilai_hasil_kerja) }}</td>
                        <td>{{ $item->nilai_total }}</td>
                        <td>{{ $item->kkm ?? '-' }}</td>
                        <td>{{ $item->kkm ? ($item->nilai_total >= $item->kkm ? 'Lulus' : 'Tidak Lulus') : '-' }}</td>
                        <td class="flex space-x-2">
                            {{-- Tombol Edit --}}
                            <a href="javascript:void(0);" 
                                class="text-blue-600 hover:text-blue-800 edit-button"
                                data-id="{{ $item->id }}"
                                data-pendaftaran="{{ $item->pendaftaran_id }}"
                                data-kedisiplinan="{{ $item->nilai_kedisiplinan }}"
                                data-inisiatif="{{ $item->nilai_inisiatif }}"
                                data-kerjasama="{{ $item->nilai_kerjasama }}"
                                data-hasil_kerja="{{ $item->nilai_hasil_kerja }}"
                                title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.25 2.25 0 113.182 3.182L7.5 19.313 3 21l1.687-4.5L16.862 3.487z" />
                                </svg>
                            </a>

                            {{-- Tombol Hapus --}}
                            <form action="{{ route('penilaian.destroy', $item->id) }}" method="POST" class="inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="text-red-600 hover:text-red-800 delete-button" title="Hapus">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Form Penilaian --}}
    <div id="form-state" class="hidden p-6 border rounded-lg border-gray-200 max-w-lg mx-auto mt-6">
        <h2 class="text-2xl font-bold text-gray-800 text-center mb-6">Form Penilaian</h2>
        <form id="penilaian-form" action="{{ route('penilaian.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="penilaian_id" id="penilaian_id" value="">
            <div>
                <label>Nama Peserta</label>
                <select name="pendaftaran_id" id="pendaftaran_id" required class="w-full border rounded px-3 py-2">
                    <option value="">-- Pilih Peserta --</option>
                    @foreach ($pendaftar as $peserta)
                        <option value="{{ $peserta->id }}">{{ $peserta->user->nama_lengkap ?? 'Tidak ada nama' }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>Kedisiplinan</label>
                <input type="number" name="nilai_kedisiplinan" id="nilai_kedisiplinan" min="0" max="100" required class="w-full border px-3 py-2 rounded">
            </div>

            <div>
                <label>Inisiatif</label>
                <input type="number" name="nilai_inisiatif" id="nilai_inisiatif" min="0" max="100" required class="w-full border px-3 py-2 rounded">
            </div>

            <div>
                <label>Kerjasama</label>
                <input type="number" name="nilai_kerjasama" id="nilai_kerjasama" min="0" max="100" required class="w-full border px-3 py-2 rounded">
            </div>

            <div>
                <label>Kinerja</label>
                <input type="number" name="nilai_hasil_kerja" id="nilai_hasil_kerja" min="0" max="100" required class="w-full border px-3 py-2 rounded">
            </div>

            <div>
                <label>Total Nilai</label>
                <input type="text" name="total_nilai" id="total_nilai" readonly class="w-full border px-3 py-2 rounded bg-gray-100">
            </div>

            <div class="flex justify-end space-x-4 mt-6">
                <button type="button" id="form-cancel-button" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Batal</button>
                <button type="submit" class="px-4 py-2 bg-navy text-white rounded hover:bg-baby-blue">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        // Ambil input & total nilai
        const inputs = ['nilai_kedisiplinan','nilai_inisiatif','nilai_kerjasama','nilai_hasil_kerja']
            .map(id => document.getElementById(id));
        const total = document.getElementById('total_nilai');

        // Fungsi hitung total nilai
        function hitungTotal() {
            let sum = 0;
            inputs.forEach(i => sum += parseInt(i.value) || 0); // bulat
            total.value = (sum / inputs.length).toFixed(2); // total 2 desimal
        }

        // Event listener untuk update total saat input berubah
        inputs.forEach(inp => inp.addEventListener('input', () => {
        
            // Bulatkan input setiap kali diubah
            inp.value = Math.round(inp.value);
            hitungTotal();
        }));

        // Tombol Batal
        document.getElementById('form-cancel-button').addEventListener('click', () => {
            document.getElementById('penilaian-form').reset();
            total.value = '';
            document.getElementById('form-state').classList.add('hidden');

            const table = document.getElementById('table-state');
            const empty = document.getElementById('empty-state');
    
            @if($penilaian->isEmpty())
                if(empty) empty.classList.remove('hidden');
                @else
                if(table) table.classList.remove('hidden');
            @endif

            const createBtn = document.getElementById('create-button');
                if(createBtn) createBtn.style.display = 'inline-block';
        });

        // Tombol Edit
        document.querySelectorAll('.edit-button').forEach(btn => {
            btn.addEventListener('click', () => {
                showForm({
                    id: btn.dataset.id,
                    pendaftaran: btn.dataset.pendaftaran,
                    kedisiplinan: btn.dataset.kedisiplinan,
                    inisiatif: btn.dataset.inisiatif,
                    kerjasama: btn.dataset.kerjasama,
                    hasil_kerja: btn.dataset.hasil_kerja
                });
            });
        });

        // Tombol Buat Penilaian
        const createButton = document.getElementById('create-button');
        const emptyCreateButton = document.getElementById('empty-create-button');
        if(createButton) createButton.addEventListener('click', () => showForm());
        if(emptyCreateButton) emptyCreateButton.addEventListener('click', () => showForm());

        // Fungsi disable peserta yang sudah ada di tabel
        function disablePesertaSudahAda() {
            const select = document.getElementById('pendaftaran_id');
            if(!select) return;

            // Ambil semua pendaftaran_id yang sudah ada di tabel
            const existingIds = Array.from(document.querySelectorAll('#table-state tbody tr')).map(tr => {
                return tr.querySelector('.edit-button')?.dataset.pendaftaran;
            }).filter(id => id);

            // Loop semua option di select
            Array.from(select.options).forEach(option => {
                // Reset text dulu supaya nggak numpuk
                option.text = option.text.replace(' (Sudah dinilai)','');

                if(existingIds.includes(option.value)) {
                    option.disabled = true;
                    option.style.backgroundColor = '#f0f0f0'; // warna abu
                    option.text += ' (Sudah dinilai)';
                } else {
                    option.disabled = false;
                    option.style.backgroundColor = '';
                }
            });
        }

        // Panggil saat form muncul
        function showForm(editData = null) {
            const formState = document.getElementById('form-state');
            formState.classList.remove('hidden');

            const table = document.getElementById('table-state');
            const empty = document.getElementById('empty-state');
            if(table) table.classList.add('hidden');
            if(empty) empty.classList.add('hidden');

            const form = document.getElementById('penilaian-form');
            form.reset();
            total.value = '';

            const createBtn = document.getElementById('create-button');
            if(createBtn) createBtn.style.display = 'none';

            // Disable peserta yang sudah ada
            disablePesertaSudahAda();

            if(editData) {
                document.getElementById('penilaian_id').value = editData.id;
                document.getElementById('pendaftaran_id').value = editData.pendaftaran;
                document.getElementById('nilai_kedisiplinan').value = Math.round(editData.kedisiplinan);
                document.getElementById('nilai_inisiatif').value = Math.round(editData.inisiatif);
                document.getElementById('nilai_kerjasama').value = Math.round(editData.kerjasama);
                document.getElementById('nilai_hasil_kerja').value = Math.round(editData.hasil_kerja);
                hitungTotal();
            }
        }

        // Panggil saat halaman load juga supaya kalau form muncul langsung update
        disablePesertaSudahAda();

        // SweetAlert untuk tombol hapus
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', function() {
                const form = this.closest('form');
                Swal.fire({
                    title: 'Apakah kamu yakin?',
                    text: "Data penilaian ini akan dihapus!",
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

        // SweetAlert notifikasi sukses setelah redirect
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 1500
        });
        @endif
    </script>
@endsection
