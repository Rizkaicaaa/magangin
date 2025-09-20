@extends('layouts.app')

@section('title', 'Kelola Info OR | MagangIn')

@section('content')

        <div class="bg-white p-8 rounded-xl shadow-lg mx-6">
            <div class="flex justify-between items-center mb-6">
                <h1 id="page-title" class="text-3xl font-bold text-gray-800">Kelola Info OR</h1>
                <button id="create-button" class="py-2 px-4 rounded-md bg-navy text-white font-semibold hover:bg-baby-blue transition-colors duration-300 {{ count($infoOrs) > 0 ? '' : 'hidden' }}">
                    + Buat Info OR
                </button>
            </div>

            <div id="content-container">
                <div id="empty-state" class="text-center p-12 {{ count($infoOrs) > 0 ? 'hidden' : '' }}">
                    <p class="text-gray-500 mb-4">
                        Belum ada Info yang dimasukkan. Silakan buat Info OR dengan klik button buat di bawah untuk membuat Info OR
                    </p>
                    <button id="empty-create-button" class="py-2 px-4 rounded-md bg-navy text-white font-semibold hover:bg-baby-blue transition-colors duration-300">
                        + Buat Info OR
                    </button>
                </div>

                <div id="table-state" class="{{ count($infoOrs) > 0 ? '' : 'hidden' }} overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul Info</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Persyaratan Umum</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Buka</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Tutup</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gambar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($infoOrs as $info)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $loop->iteration }}.</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $info->judul }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $info->deskripsi }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $info->persyaratan_umum }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($info->tanggal_buka)->format('d F Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($info->tanggal_tutup)->format('d F Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $info->periode }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $info->gambar }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $info->status }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if($info->status == 'buka')
                                            <button data-id="{{ $info->id }}" class="text-navy hover:text-red-900 close-button">Tutup</button>
                                        @else
                                            <span class="text-gray-400">Telah ditutup</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Belum ada Info yang dimasukkan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div id="form-state" class="hidden p-8 border-2 rounded-lg border-gray-200 max-w-lg mx-auto">
                    <h2 class="text-2xl font-bold text-gray-800 text-center mb-6">Form Info OR</h2>
                    <form action="{{ route('info-or.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="judul-info" class="block text-sm font-medium text-gray-700">Judul Info</label>
                            <input type="text" id="judul-info" name="judul" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2" required>
                            @error('judul')
                                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="deskripsi-info" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <textarea id="deskripsi-info" name="deskripsi" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2" required></textarea>
                            @error('deskripsi')
                                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="persyaratan-umum" class="block text-sm font-medium text-gray-700">Persyaratan Umum</label>
                            <textarea id="persyaratan-umum" name="persyaratan_umum" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2"></textarea>
                        </div>
                        <div>
                            <label for="tanggal-buka" class="block text-sm font-medium text-gray-700">Tanggal Buka</label>
                            <input type="date" id="tanggal-buka" name="tanggal_buka" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2">
                            @error('tanggal_buka')
                                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="tanggal-tutup" class="block text-sm font-medium text-gray-700">Tanggal Tutup</label>
                            <input type="date" id="tanggal-tutup" name="tanggal_tutup" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2">
                            @error('tanggal_tutup')
                                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="periode" class="block text-sm font-medium text-gray-700">Periode</label>
                            <input type="text" id="periode" name="periode" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2">
                            @error('periode')
                                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="gambar" class="block text-sm font-medium text-gray-700">Gambar (URL)</label>
                            <input type="text" id="gambar" name="gambar" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2">
                            @error('gambar')
                                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2">
                                <option value="buka">Buka</option>
                                <option value="tutup">Tutup</option>
                            </select>
                            @error('status')
                                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="flex justify-end space-x-4 mt-6">
                            <button id="form-cancel-button" type="button" class="py-2 px-6 rounded-md bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 transition-colors duration-300">
                                Batal
                            </button>
                            <button type="submit" class="py-2 px-6 rounded-md bg-navy text-white font-semibold hover:bg-baby-blue transition-colors duration-300">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>

                <div id="delete-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
                    <div class="bg-white p-6 rounded-lg shadow-xl text-center">
                        <p class="text-lg font-semibold text-gray-800 mb-4">Apakah Anda yakin ingin menutup pendaftaran ini?</p>
                        <div class="flex justify-center space-x-4">
                            <button id="modal-cancel-button" type="button" class="py-2 px-6 rounded-md bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 transition-colors duration-300">
                                Tidak
                            </button>
                            <form id="closeForm" method="POST" action="">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="py-2 px-6 rounded-md bg-navy text-white font-semibold hover:bg-baby-blue transition-colors duration-300">
                                    Yakin
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection

@section('scripts')
    <script>
        const emptyState = document.getElementById('empty-state');
        const tableState = document.getElementById('table-state');
        const formState = document.getElementById('form-state');
        const deleteModal = document.getElementById('delete-modal');
        const pageTitle = document.getElementById('page-title');
        const createButton = document.getElementById('create-button');
        const emptyCreateButton = document.getElementById('empty-create-button');
        const formCancelButton = document.getElementById('form-cancel-button');
        const modalCancelButton = document.getElementById('modal-cancel-button');
        const closeForm = document.getElementById('closeForm');

        function manageView() {
            const hasData = document.querySelectorAll('#table-state table tbody tr:not(:has(td[colspan]))').length > 0;
            
            if (!hasData) {
                emptyState.classList.remove('hidden');
                tableState.classList.add('hidden');
                createButton.classList.add('hidden');
            } else {
                emptyState.classList.add('hidden');
                tableState.classList.remove('hidden');
                createButton.classList.remove('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            manageView();
        });

        createButton.addEventListener('click', () => {
            tableState.classList.add('hidden');
            emptyState.classList.add('hidden');
            formState.classList.remove('hidden');
            createButton.classList.add('hidden');
            pageTitle.textContent = 'Form Info OR';
        });

        emptyCreateButton.addEventListener('click', () => {
            createButton.click();
        });

        formCancelButton.addEventListener('click', () => {
            formState.classList.add('hidden');
            manageView();
        });
        
        modalCancelButton.addEventListener('click', () => {
            deleteModal.classList.add('hidden');
        });

        document.querySelectorAll('.close-button').forEach(button => {
            button.addEventListener('click', (e) => {
                const id = e.target.dataset.id;
                closeForm.action = `{{ url('kelola-info-or') }}/${id}/tutup`;
                deleteModal.classList.remove('hidden');
            });
        });
    </script>
@endsection