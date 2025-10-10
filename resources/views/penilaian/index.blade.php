@extends('layouts.app')

@section('title', 'Kelola Penilaian | MagangIn')

@section('content')

<style>
    table th, table td {
        text-align: center;
        vertical-align: middle;
        padding: 0.75rem;
    }
    table tbody tr:hover { background-color: #f9fafb; }
</style>

<div class="bg-white p-8 rounded-xl shadow-lg mx-6">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Kelola Penilaian Mahasiswa Magang</h1>
        <button id="create-button" class="py-2 px-4 rounded-md bg-navy text-white font-semibold hover:bg-baby-blue transition-colors duration-300">
            Buat Penilaian
        </button>
    </div>

    {{-- Empty State --}}
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
                    <td>{{ $item->nilai_total >= 70 ? 'Lulus' : 'Tidak Lulus' }}</td>
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

    {{-- Form Create --}}
    <div id="create-form" class="hidden p-6 border rounded-lg border-gray-200 max-w-lg mx-auto mt-6">
        <h2 class="text-2xl font-bold text-gray-800 text-center mb-6">Form Buat Penilaian</h2>
        <form id="form-create" action="{{ route('penilaian.store') }}" method="POST">
            @csrf
            {{-- Nama Peserta --}}
            <div>
                <label>Nama Peserta</label>
                <select name="pendaftaran_id" id="pendaftaran_create" required class="w-full border rounded px-3 py-2">
                    <option value="">-- Pilih Peserta --</option>
                    @foreach ($pendaftar as $peserta)
                        <option value="{{ $peserta->id }}">{{ $peserta->user->nama_lengkap ?? 'Tidak ada nama' }}</option>
                    @endforeach
                </select>
            </div>
            @foreach(['kedisiplinan','inisiatif','kerjasama','hasil_kerja'] as $field)
            <div>
                <label>{{ ucfirst($field) }}</label>
                <input type="number" name="nilai_{{ $field }}" id="nilai_{{ $field }}_create" min="0" max="100" required class="w-full border px-3 py-2 rounded">
            </div>
            @endforeach
            <div>
                <label>Total Nilai</label>
                <input type="text" id="total_nilai_create" readonly class="w-full border px-3 py-2 rounded bg-gray-100">
            </div>
            <div class="flex justify-end space-x-4 mt-6">
                <button type="button" id="cancel-create" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Batal</button>
                <button type="submit" class="px-4 py-2 bg-navy text-white rounded hover:bg-baby-blue">Simpan</button>
            </div>
        </form>
    </div>

    {{-- Form Edit --}}
    <div id="edit-form" class="hidden p-6 border rounded-lg border-gray-200 max-w-lg mx-auto mt-6">
        <h2 class="text-2xl font-bold text-gray-800 text-center mb-6">Form Edit Penilaian</h2>
        <form id="form-edit" action="{{ route('penilaian.store') }}" method="POST">
            @csrf
            <input type="hidden" name="penilaian_id" id="penilaian_id_edit">
            <div>
                <label>Nama Peserta</label>
                <select name="pendaftaran_id" id="pendaftaran_edit" required class="w-full border rounded px-3 py-2">
                    <option value="">-- Pilih Peserta --</option>
                    @foreach ($pendaftar as $peserta)
                        <option value="{{ $peserta->id }}">{{ $peserta->user->nama_lengkap ?? 'Tidak ada nama' }}</option>
                    @endforeach
                </select>
            </div>
            @foreach(['kedisiplinan','inisiatif','kerjasama','hasil_kerja'] as $field)
            <div>
                <label>{{ ucfirst($field) }}</label>
                <input type="number" name="nilai_{{ $field }}" id="nilai_{{ $field }}_edit" min="0" max="100" required class="w-full border px-3 py-2 rounded">
            </div>
            @endforeach
            <div>
                <label>Total Nilai</label>
                <input type="text" id="total_nilai_edit" readonly class="w-full border px-3 py-2 rounded bg-gray-100">
            </div>
            <div class="flex justify-end space-x-4 mt-6">
                <button type="button" id="cancel-edit" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Batal</button>
                <button type="submit" class="px-4 py-2 bg-navy text-white rounded hover:bg-baby-blue">Simpan</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // ==== CREATE FORM ====
    const inputsCreate = ['nilai_kedisiplinan_create','nilai_inisiatif_create','nilai_kerjasama_create','nilai_hasil_kerja_create']
        .map(id => document.getElementById(id));
    const totalCreate = document.getElementById('total_nilai_create');

    function hitungTotalCreate() {
        let sum = 0;
        inputsCreate.forEach(i => sum += parseInt(i.value) || 0);
        totalCreate.value = (sum / inputsCreate.length).toFixed(2);
    }

    inputsCreate.forEach(inp => inp.addEventListener('input', () => {
        inp.value = Math.round(inp.value);
        hitungTotalCreate();
    }));

    // ==== EDIT FORM ====
    const inputsEdit = ['nilai_kedisiplinan_edit','nilai_inisiatif_edit','nilai_kerjasama_edit','nilai_hasil_kerja_edit']
        .map(id => document.getElementById(id));
    const totalEdit = document.getElementById('total_nilai_edit');

    function hitungTotalEdit() {
        let sum = 0;
        inputsEdit.forEach(i => sum += parseInt(i.value) || 0);
        totalEdit.value = (sum / inputsEdit.length).toFixed(2);
    }

    inputsEdit.forEach(inp => inp.addEventListener('input', () => {
        inp.value = Math.round(inp.value);
        hitungTotalEdit();
    }));

    // ==== Disable peserta yang sudah dinilai ====
    function disablePeserta(selectId, currentEditId = null) {
        const select = document.getElementById(selectId);
        if(!select) return;

        const existingIds = Array.from(document.querySelectorAll('#table-state tbody tr .edit-button'))
            .map(btn => btn.dataset.pendaftaran);

        Array.from(select.options).forEach(option => {
            option.text = option.text.replace(' (Sudah dinilai)','');
            if(existingIds.includes(option.value) && option.value !== currentEditId) {
                option.disabled = true;
                option.style.backgroundColor = '#f0f0f0';
                option.text += ' (Sudah dinilai)';
            } else {
                option.disabled = false;
                option.style.backgroundColor = '';
            }
        });
    }

    // ==== Tombol Create ====
    document.getElementById('create-button').addEventListener('click', () => {
        document.getElementById('create-form').classList.remove('hidden');
        document.getElementById('edit-form').classList.add('hidden');
        document.getElementById('table-state').classList.add('hidden');
        document.getElementById('empty-state')?.classList.add('hidden');

        // Disable peserta
        disablePeserta('pendaftaran_create');
    });

    document.getElementById('empty-create-button')?.addEventListener('click', () => {
        document.getElementById('create-button').click();
    });

    // ==== Cancel Create ====
    document.getElementById('cancel-create').addEventListener('click', () => {
        document.getElementById('form-create').reset();
        totalCreate.value = '';
        document.getElementById('create-form').classList.add('hidden');
        if({{ $penilaian->isEmpty() ? 'true':'false' }}) document.getElementById('empty-state').classList.remove('hidden');
        else document.getElementById('table-state').classList.remove('hidden');
    });

    // ==== Tombol Edit ====
    document.querySelectorAll('.edit-button').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('edit-form').classList.remove('hidden');
            document.getElementById('create-form').classList.add('hidden');
            document.getElementById('table-state').classList.add('hidden');
            document.getElementById('empty-state')?.classList.add('hidden');

            document.getElementById('penilaian_id_edit').value = btn.dataset.id;
            document.getElementById('pendaftaran_edit').value = btn.dataset.pendaftaran;
            document.getElementById('nilai_kedisiplinan_edit').value = Math.round(btn.dataset.kedisiplinan);
            document.getElementById('nilai_inisiatif_edit').value = Math.round(btn.dataset.inisiatif);
            document.getElementById('nilai_kerjasama_edit').value = Math.round(btn.dataset.kerjasama);
            document.getElementById('nilai_hasil_kerja_edit').value = Math.round(btn.dataset.hasil_kerja);

            hitungTotalEdit();
            disablePeserta('pendaftaran_edit', btn.dataset.pendaftaran);
        });
    });

    // ==== Cancel Edit ====
    document.getElementById('cancel-edit').addEventListener('click', () => {
        document.getElementById('form-edit').reset();
        totalEdit.value = '';
        document.getElementById('edit-form').classList.add('hidden');
        document.getElementById('table-state').classList.remove('hidden');
    });

    // ==== SweetAlert Hapus ====
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
                if(result.isConfirmed) form.submit();
            });
        });
    });

    // ==== SweetAlert sukses ====
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
