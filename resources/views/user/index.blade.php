@extends('layouts.app')

@section('title', 'Kelola User | MagangIn')

@section('content')

<div class="bg-white p-8 rounded-xl shadow-lg mx-6">
    <div class="flex justify-between items-center mb-6">
        <h1 id="page-title" class="text-3xl font-bold text-gray-800">Kelola User</h1>
        <button id="create-button" class="py-2 px-4 rounded-md bg-navy text-white font-semibold hover:bg-baby-blue transition-colors duration-300 {{ count($users) > 0 ? '' : 'hidden' }}">
            + Buat User
        </button>
    </div>

    <div id="content-container">
        {{-- Bagian Empty State --}}
        <div id="empty-state" class="text-center p-12 {{ count($users) > 0 ? 'hidden' : '' }}">
            <p class="text-gray-500 mb-4">
                Belum ada user yang terdaftar. Silakan tambahkan user dengan klik button buat di bawah.
            </p>
            <button id="empty-create-button" class="py-2 px-4 rounded-md bg-navy text-white font-semibold hover:bg-baby-blue transition-colors duration-300">
                + Buat User
            </button>
        </div>

        {{-- Bagian Tabel Data --}}
        <div id="table-state" class="{{ count($users) > 0 ? '' : 'hidden' }} overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Lengkap</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIM</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Telp</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Daftar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $loop->iteration }}.</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->nama_lengkap }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->role }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->nim ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->no_telp ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($user->tanggal_daftar)->format('d F Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->status }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button data-id="{{ $user->id }}" data-action="edit" class="text-blue-500 hover:underline edit-button mr-2">Edit</button>
                                <button data-id="{{ $user->id }}" data-action="delete" class="text-red-500 hover:underline delete-button">Hapus</button>
                                
                                    {{-- <button data-id="{{ $user->id }}" data-action="edit" class="text-blue-500 hover:text-blue-800 edit-button mr-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.25 2.25 0 113.182 3.182L7.5 19.313 3 21l1.687-4.5L16.862 3.487z" />
                                        </svg>
                                    </button>
                                    <button data-id="{{ $user->id }}" data-action="delete" class="text-red-500 hover:text-red-800 delete-button">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button> --}}
                                
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">
                                Belum ada user yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
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

        function manageView() {
            const hasData = document.querySelectorAll('#table-state table tbody tr:not(:has(td[colspan]))').length > 0;
            const emptyState = document.getElementById('empty-state');
            const tableState = document.getElementById('table-state');
            
            if (!hasData) {
                emptyState.classList.remove('hidden');
                tableState.classList.add('hidden');
                if (createButton) createButton.classList.add('hidden');
            } else {
                emptyState.classList.add('hidden');
                tableState.classList.remove('hidden');
                if (createButton) createButton.classList.remove('hidden');
            }
        }

        manageView();

        // Tampilkan modal form
        if (createButton) {
            createButton.addEventListener('click', () => {
                formModal.classList.remove('hidden');
                document.getElementById('form-title').textContent = 'Tambah User Baru';
                document.getElementById('user-form').reset();
                document.getElementById('user-form').action = "{{ route('users.store') }}";
                document.getElementById('password-field').classList.remove('hidden'); // Tampilkan field password saat tambah
                document.getElementById('method_field').remove(); // Hapus method field jika ada
            });
        }
        if (emptyCreateButton) {
            emptyCreateButton.addEventListener('click', () => {
                formModal.classList.remove('hidden');
                document.getElementById('form-title').textContent = 'Tambah User Baru';
                document.getElementById('user-form').reset();
                document.getElementById('user-form').action = "{{ route('users.store') }}";
                document.getElementById('password-field').classList.remove('hidden');
                document.getElementById('method_field').remove();
            });
        }

        // Tutup modal form
        closeFormModalButton.addEventListener('click', () => formModal.classList.add('hidden'));
        formCancelButton.addEventListener('click', () => formModal.classList.add('hidden'));

        // Tampilkan modal hapus
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', (e) => {
                const id = e.target.dataset.id;
                // Ubah URL kembali ke yang lama
                deleteForm.action = `/users/${id}/destroy`;
                deleteModal.classList.remove('hidden');
            });
        });

        // Tutup modal hapus
        modalCancelButton.addEventListener('click', () => deleteModal.classList.add('hidden'));

        // Tampilkan modal edit
        document.querySelectorAll('.edit-button').forEach(button => {
            button.addEventListener('click', (e) => {
                const id = e.target.dataset.id;
                
                // Fetch data user dari server (menggunakan fetch API)
                fetch(`/users/${id}/edit`)
                    .then(response => response.json())
                    .then(data => {
                        // Isi form dengan data yang didapat
                        document.getElementById('form-title').textContent = 'Edit User';
                        document.getElementById('user-form').action = `/users/${id}`;
                        document.getElementById('user-form').innerHTML += `<input type="hidden" name="_method" value="PUT" id="method_field">`;
                        document.getElementById('nama_lengkap').value = data.nama_lengkap;
                        document.getElementById('email').value = data.email;
                        document.getElementById('role').value = data.role;
                        document.getElementById('nim').value = data.nim || '';
                        document.getElementById('no_telp').value = data.no_telp || '';
                        document.getElementById('status').value = data.status;
                        document.getElementById('dinas_id').value = data.dinas_id || '';
                        
                        // Sembunyikan field password saat edit
                        document.getElementById('password-field').classList.add('hidden');

                        const passwordInput = document.getElementById('password');
                        if (passwordInput) {
                            passwordInput.removeAttribute('required');
                        }
                        const confirmPasswordInput = document.getElementById('password_confirmation');
                        if (confirmPasswordInput) {
                            confirmPasswordInput.removeAttribute('required');
                        }
                        
                        formModal.classList.remove('hidden');
                    });
            });
        });
    });
</script>
@endsection