@extends('layouts.app')

@section('title', 'Kelola User | MagangIn')

@section('content')

<div class="bg-white p-8 rounded-xl shadow-lg mx-6">
    <div class="flex justify-between items-center mb-6">
        <h1 id="page-title" class="text-3xl font-bold text-gray-800">Kelola User</h1>
        <button id="create-button"
            class="py-2 px-4 rounded-md bg-navy text-white font-semibold hover:bg-baby-blue transition-colors duration-300">
            Buat User
        </button>
    </div>

    {{-- Pembagian Data User di sisi View --}}
    @php
    $adminUsers = $users->filter(fn($user) => $user->role !== 'mahasiswa');
    $mahasiswaUsers = $users->filter(fn($user) => $user->role === 'mahasiswa');
    $hasData = count($users) > 0;
    $hasAdminData = count($adminUsers) > 0;
    $hasMahasiswaData = count($mahasiswaUsers) > 0;
    @endphp

    <div id="content-container">
        {{-- Bagian Empty State Global --}}
        <div id="empty-state" class="text-center p-12 {{ $hasData ? 'hidden' : '' }}">
            <p class="text-gray-500 mb-4">
                Belum ada user yang terdaftar. Silakan tambahkan user dengan klik button buat di bawah.
            </p>
            <button id="empty-create-button"
                class="py-2 px-4 rounded-md bg-navy text-white font-semibold hover:bg-baby-blue transition-colors duration-300">
                + Buat User
            </button>
        </div>

        {{-- ========================================================== --}}
        {{-- TABEL 1: USER ADMINISTRATOR (Superadmin, Admin) --}}
        {{-- ========================================================== --}}
        <h2 class="text-2xl font-semibold text-gray-700 mb-4 mt-8 {{ $hasAdminData ? '' : 'hidden' }}">Admin & Super
            Admin</h2>

        <div id="admin-table-state" class="{{ $hasAdminData ? '' : 'hidden' }} overflow-x-auto mb-10">
            <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama
                            Lengkap</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIM
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.
                            Telp</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal Daftar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($adminUsers as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $loop->iteration }}.</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->nama_lengkap }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($user->role) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->nim ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->no_telp ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($user->created_at)->format('d F Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($user->status) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center space-x-2">
                                {{-- Tombol EDIT (Icon) --}}
                                <button data-id="{{ $user->id }}" data-action="edit" title="Edit User"
                                    class="edit-button inline-flex items-center p-2 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-full transition-all duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.862 3.487a2.25 2.25 0 113.182 3.182L7.5 19.313 3 21l1.687-4.5L16.862 3.487z" />
                                    </svg>
                                </button>

                                {{-- Tombol HAPUS (Icon) --}}
                                <button data-id="{{ $user->id }}" data-action="delete" title="Hapus User"
                                    class="delete-button inline-flex items-center p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-full transition-all duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ========================================================== --}}
        {{-- TABEL 2: USER MAHASISWA (Hanya Edit) --}}
        {{-- ========================================================== --}}
        <h2 class="text-2xl font-semibold text-gray-700 mb-4 mt-8 {{ $hasMahasiswaData ? '' : 'hidden' }}">User
            Mahasiswa</h2>

        <div id="mahasiswa-table-state" class="{{ $hasMahasiswaData ? '' : 'hidden' }} overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama
                            Lengkap</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIM
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.
                            Telp</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($mahasiswaUsers as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $loop->iteration }}.</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->nama_lengkap }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->nim ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->no_telp ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($user->status) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center">
                                {{-- Tombol EDIT (Icon) --}}
                                <button data-id="{{ $user->id }}" data-action="edit" title="Edit User"
                                    class="edit-button inline-flex items-center p-2 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-full transition-all duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.862 3.487a2.25 2.25 0 113.182 3.182L7.5 19.313 3 21l1.687-4.5L16.862 3.487z" />
                                    </svg>
                                </button>
                                {{-- Tombol Hapus Dihilangkan Sesuai Permintaan --}}
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>

{{-- Modal Tambah/Edit User --}}
@include('user.components.form')

{{-- Modal Hapus User --}}
@include('user.components.delete-modal')

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const createButton = document.getElementById('create-button');
    const emptyCreateButton = document.getElementById('empty-create-button');
    const formModal = document.getElementById('form-modal');
    const closeFormModalButton = document.getElementById('close-form-modal');
    const formCancelButton = document.getElementById('form-cancel-button');
    const deleteModal = document.getElementById('delete-modal');
    const modalCancelButton = document.getElementById('modal-cancel-button');
    const deleteForm = document.getElementById('deleteForm');

    // Elemen-elemen state
    const emptyState = document.getElementById('empty-state');
    // Tidak perlu ambil elemen adminTableState dan mahasiswaTableState di JS karena sudah dihandle Blade
    // const adminTableState = document.getElementById('admin-table-state');
    // const mahasiswaTableState = document.getElementById('mahasiswa-table-state');

    function manageView() {
        // Cek apakah ada data secara keseluruhan (mengandalkan variabel global $users dari Blade)
        // Note: Ini hanya berfungsi sebagai fallback, logika utama sudah dihandle di Blade
        const totalDataRows = {
            {
                count($users)
            }
        }; // Menggunakan variabel Blade
        const hasData = totalDataRows > 0;

        // Logika untuk Empty State Global
        if (!hasData) {
            emptyState.classList.remove('hidden');
            // Tidak perlu mengubah display table state, biarkan Blade yang mengaturnya
            if (createButton) createButton.classList.add('hidden');
        } else {
            emptyState.classList.add('hidden');
            if (createButton) createButton.classList.remove('hidden');
        }
    }

    manageView();

    // Fungsi untuk memastikan input method PUT/DELETE dihapus saat mode CREATE
    function cleanupMethodField() {
        const methodField = document.getElementById('method_field');
        if (methodField) {
            methodField.remove();
        }
    }

    // Tampilkan modal form untuk CREATE
    const setupCreateForm = () => {
        formModal.classList.remove('hidden');
        document.getElementById('form-title').textContent = 'Tambah User Baru';
        document.getElementById('user-form').reset();
        document.getElementById('user-form').action = "{{ route('users.store') }}";
        document.getElementById('password-field').classList.remove('hidden');
        document.getElementById('password').setAttribute('required',
        'required'); // Pastikan password required saat membuat user baru

        // Hapus method field jika ada
        cleanupMethodField();
    };

    if (createButton) {
        createButton.addEventListener('click', setupCreateForm);
    }
    if (emptyCreateButton) {
        emptyCreateButton.addEventListener('click', setupCreateForm);
    }

    // Tutup modal form
    closeFormModalButton.addEventListener('click', () => formModal.classList.add('hidden'));
    formCancelButton.addEventListener('click', () => formModal.classList.add('hidden'));

    // Tampilkan modal hapus
    // Menggunakan delegasi event pada elemen body untuk memastikan tombol yang di-render Blade terdeteksi
    document.querySelectorAll('.delete-button').forEach(button => {
        button.addEventListener('click', (e) => {
            const id = e.currentTarget.dataset.id; // Menggunakan currentTarget untuk button
            // Ubah URL untuk DELETE
            deleteForm.action = `/users/${id}/destroy`;
            deleteModal.classList.remove('hidden');
        });
    });

    // Tutup modal hapus
    modalCancelButton.addEventListener('click', () => deleteModal.classList.add('hidden'));

    // Tampilkan modal edit
    document.querySelectorAll('.edit-button').forEach(button => {
        button.addEventListener('click', (e) => {
            const id = e.currentTarget.dataset.id; // Menggunakan currentTarget untuk button

            // Hapus method field lama jika ada
            cleanupMethodField();

            // Fetch data user dari server (menggunakan fetch API)
            fetch(`/users/${id}/edit`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Isi form dengan data yang didapat
                    document.getElementById('form-title').textContent = 'Edit User';
                    document.getElementById('user-form').action = `/users/${id}`;

                    // Tambahkan method field untuk PUT
                    const form = document.getElementById('user-form');
                    const methodFieldHtml =
                        `<input type="hidden" name="_method" value="PUT" id="method_field">`;
                    form.insertAdjacentHTML('afterbegin', methodFieldHtml);

                    document.getElementById('nama_lengkap').value = data.nama_lengkap;
                    document.getElementById('email').value = data.email;
                    document.getElementById('role').value = data.role;
                    document.getElementById('nim').value = data.nim || '';
                    document.getElementById('no_telp').value = data.no_telp || '';
                    document.getElementById('status').value = data.status;
                    document.getElementById('dinas_id').value = data.dinas_id || '';

                    // Sembunyikan field password saat edit
                    document.getElementById('password-field').classList.add('hidden');
                    document.getElementById('password').removeAttribute('required');

                    formModal.classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error fetching user data:', error);
                    // Di lingkungan live, Anda bisa menampilkan pesan error yang ramah pengguna
                });
        });
    });
});
</script>
@endsection