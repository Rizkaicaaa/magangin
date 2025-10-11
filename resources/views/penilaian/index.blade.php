@extends('layouts.app')

@section('title', 'Kelola Penilaian | MagangIn')

@section('content')
<div class="bg-white p-8 rounded-xl shadow-lg mx-6">

    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-8 space-y-4 md:space-y-0">
        <h1 class="text-3xl font-bold text-gray-800">
            Kelola Penilaian Mahasiswa Magang
        </h1>

        <div class="flex flex-wrap justify-start md:justify-end gap-3">
            {{-- Upload Template Sertifikat --}}
            <a href="{{ route('template.upload') }}"
                class="flex items-center gap-2 py-2 px-4 rounded-md bg-blue-100 text-blue-700 font-semibold hover:bg-blue-200 transition-all duration-300 shadow-sm border border-blue-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Upload Template
            </a>

            {{-- Buat Pengumuman Kelulusan --}}
            <a href="{{ route('pengumuman.kelulusan') }}"
                class="flex items-center gap-2 py-2 px-4 rounded-md bg-green-100 text-green-700 font-semibold hover:bg-green-200 transition-all duration-300 shadow-sm border border-green-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8 7V3m8 4V3m-9 8h10m-9 4h8m1 5H6a2 2 0 01-2-2V7a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2z" />
                </svg>
                Pengumuman Kelulusan
            </a>

            {{-- Buat Penilaian --}}
            <button id="create-button"
                class="flex items-center gap-2 py-2 px-4 rounded-md bg-navy text-white font-semibold hover:bg-baby-blue transition-colors duration-300 shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Buat Penilaian
            </button>
        </div>
    </div>

    {{-- Empty State --}}
    @if ($penilaian->isEmpty())
    <div id="empty-state" class="text-center p-12">
        <p class="text-gray-500 mb-4">
            Belum ada Data yang dimasukkan. Silakan klik tombol buat di bawah untuk membuat Penilaian Magang
        </p>
        <button id="empty-create-button"
            class="py-2 px-4 rounded-md bg-navy text-white font-semibold hover:bg-baby-blue transition-colors duration-300">
            Buat Penilaian
        </button>
    </div>
    @endif

    {{-- Table State --}}
    <div id="table-state" class="overflow-x-auto {{ $penilaian->isEmpty() ? 'hidden' : '' }}">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Peserta</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kedisiplinan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Inisiatif</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kerjasama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kinerja</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Nilai</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Magang</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($penilaian as $index => $item)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $item->pendaftaran->user->nama_lengkap ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ intval($item->nilai_kedisiplinan) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ intval($item->nilai_inisiatif) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ intval($item->nilai_kerjasama) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ intval($item->nilai_hasil_kerja) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ number_format($item->nilai_total, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <span
                            class="{{ $item->nilai_total >= 70 ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold' }}">
                            {{ $item->nilai_total >= 70 ? 'Lulus' : 'Tidak Lulus' }}
                        </span>
                    </td>
                    <td
                        class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium flex items-center justify-center space-x-2">
                        {{-- Edit --}}
                        <a href="javascript:void(0);" class="text-blue-600 hover:text-blue-800 edit-button"
                            data-id="{{ $item->id }}" data-pendaftaran="{{ $item->pendaftaran_id }}"
                            data-kedisiplinan="{{ $item->nilai_kedisiplinan }}"
                            data-inisiatif="{{ $item->nilai_inisiatif }}" data-kerjasama="{{ $item->nilai_kerjasama }}"
                            data-hasil_kerja="{{ $item->nilai_hasil_kerja }}" title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.25 2.25 0 113.182 3.182L7.5 19.313 
                                    3 21l1.687-4.5L16.862 3.487z" />
                            </svg>
                        </a>

                        {{-- Hapus --}}
                        <form action="{{ route('penilaian.destroy', $item->id) }}" method="POST"
                            class="inline delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="text-red-600 hover:text-red-800 delete-button" title="Hapus">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862
                                        a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4
                                        a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
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
                <input type="number" name="nilai_{{ $field }}" id="nilai_{{ $field }}_create" min="0" max="100" required
                    class="w-full border px-3 py-2 rounded">
            </div>
            @endforeach

            <div>
                <label>Total Nilai</label>
                <input type="text" id="total_nilai_create" readonly class="w-full border px-3 py-2 rounded bg-gray-100">
            </div>

            <div class="flex justify-end space-x-4 mt-6">
                <button type="button" id="cancel-create"
                    class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Batal</button>
                <button type="submit" class="px-4 py-2 bg-navy text-white rounded hover:bg-baby-blue">Simpan</button>
            </div>
        </form>
    </div>

    {{-- Form Edit --}}
    <div id="edit-form" class="hidden p-6 border rounded-lg border-gray-200 max-w-lg mx-auto mt-6">
        <h2 class="text-2xl font-bold text-gray-800 text-center mb-6">Form Edit Penilaian</h2>
        <form id="form-edit" method="POST">
            @csrf
            @method('PUT')
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
                <input type="number" name="nilai_{{ $field }}" id="nilai_{{ $field }}_edit" min="0" max="100" required
                    class="w-full border px-3 py-2 rounded">
            </div>
            @endforeach

            <div>
                <label>Total Nilai</label>
                <input type="text" id="total_nilai_edit" readonly class="w-full border px-3 py-2 rounded bg-gray-100">
            </div>

            <div class="flex justify-end space-x-4 mt-6">
                <button type="button" id="cancel-edit"
                    class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Batal</button>
                <button type="submit" class="px-4 py-2 bg-navy text-white rounded hover:bg-baby-blue">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
// ==== Hitung Total Otomatis ====
const inputsCreate = ['nilai_kedisiplinan_create', 'nilai_inisiatif_create', 'nilai_kerjasama_create',
        'nilai_hasil_kerja_create'
    ]
    .map(id => document.getElementById(id));
const totalCreate = document.getElementById('total_nilai_create');

function hitungTotalCreate() {
    let sum = 0;
    inputsCreate.forEach(i => sum += Number(i.value) || 0);
    totalCreate.value = (sum / inputsCreate.length).toFixed(2);
}
inputsCreate.forEach(inp => inp.addEventListener('input', hitungTotalCreate));

const inputsEdit = ['nilai_kedisiplinan_edit', 'nilai_inisiatif_edit', 'nilai_kerjasama_edit', 'nilai_hasil_kerja_edit']
    .map(id => document.getElementById(id));
const totalEdit = document.getElementById('total_nilai_edit');

function hitungTotalEdit() {
    let sum = 0;
    inputsEdit.forEach(i => sum += Number(i.value) || 0);
    totalEdit.value = (sum / inputsEdit.length).toFixed(2);
}
inputsEdit.forEach(inp => inp.addEventListener('input', hitungTotalEdit));

// ==== Tombol Create & Cancel ====
const createForm = document.getElementById('create-form');
const editForm = document.getElementById('edit-form');
const tableState = document.getElementById('table-state');
document.getElementById('create-button').addEventListener('click', () => {
    createForm.classList.remove('hidden');
    tableState.classList.add('hidden');
});
document.getElementById('empty-create-button').addEventListener('click', () => {
    createForm.classList.remove('hidden');
    document.getElementById('empty-state').classList.add('hidden');
});
document.getElementById('cancel-create').addEventListener('click', () => {
    createForm.classList.add('hidden');
    tableState.classList.remove('hidden');
});
document.getElementById('cancel-edit').addEventListener('click', () => {
    editForm.classList.add('hidden');
    tableState.classList.remove('hidden');
});

// ==== Edit Button ====
document.querySelectorAll('.edit-button').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        const url = '{{ url("penilaian") }}/' + id;
        document.getElementById('form-edit').action = url;
        document.getElementById('penilaian_id_edit').value = id;
        document.getElementById('pendaftaran_edit').value = btn.dataset.pendaftaran;
        document.getElementById('nilai_kedisiplinan_edit').value = btn.dataset.kedisiplinan;
        document.getElementById('nilai_inisiatif_edit').value = btn.dataset.inisiatif;
        document.getElementById('nilai_kerjasama_edit').value = btn.dataset.kerjasama;
        document.getElementById('nilai_hasil_kerja_edit').value = btn.dataset.hasil_kerja;
        hitungTotalEdit();
        editForm.classList.remove('hidden');
        tableState.classList.add('hidden');
    });
});

// ==== SweetAlert Delete ====
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
            if (result.isConfirmed) form.submit();
        });
    });
});

// ==== Notif Sukses ====
@if(session('success'))
Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: '{{ session('
    success ') }}',
    showConfirmButton: false,
    timer: 1500
});
@endif
</script>
@endsection