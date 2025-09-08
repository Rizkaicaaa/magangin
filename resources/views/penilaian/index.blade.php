@extends('layouts.app')

@section('title', 'Kelola Penilaian | MagangIn')

@section('content')
    <div class="bg-white p-8 rounded-xl shadow-lg mx-6">

        <div class="flex justify-between items-center mb-6">
            <h1 id="page-title" class="text-3xl font-bold text-gray-800">Kelola Penilaian Mahasiswa Magang</h1>
            <button id="create-button" class="py-2 px-4 rounded-md bg-navy text-white font-semibold hover:bg-baby-blue transition-colors duration-300">
                Buat Penilaian
            </button>
        </div>

        <div id="content-container">

            <div id="empty-state" class="text-center p-12">
                <p class="text-gray-500 mb-4">
                    Belum ada Data yang dimasukkan. Silakan klik tombol buat di bawah untuk membuat Penilaian Magang
                </p>
                <button id="empty-create-button" class="py-2 px-4 rounded-md bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 transition-colors duration-300">
                    Buat Penilaian
                </button>
            </div>

            <div id="table-state" class="hidden overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Peserta</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kegiatan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kehadiran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kinerja</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inisiatif</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="table-body" class="bg-white divide-y divide-gray-200">
                        </tbody>
                </table>
            </div>
            
            <div id="form-state" class="hidden p-8 border-2 rounded-lg border-gray-200 max-w-lg mx-auto">
                <h2 class="text-2xl font-bold text-gray-800 text-center mb-6">Form Penilaian</h2>
                <form class="space-y-4">
                    <div>
                        <label for="nama-peserta" class="block text-sm font-medium text-gray-700">Nama</label>
                        <select id="nama-peserta" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2">
                            <option value="Raffi Ahmad">Raffi Ahmad</option>
                            <option value="Siti Nurhaliza">Siti Nurhaliza</option>
                            <option value="Budi Santoso">Budi Santoso</option>
                        </select>
                    </div>
                    <div>
                        <label for="kegiatan" class="block text-sm font-medium text-gray-700">Kegiatan</label>
                        <input type="text" id="kegiatan" value="BEM VISIT" disabled class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 px-4 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Penilaian</label>
                        <div class="space-y-2 mt-2">
                            <div class="flex items-center">
                                <label for="kehadiran" class="w-1/3 text-sm text-gray-600">Kehadiran</label>
                                <input type="number" id="kehadiran" value="80" class="w-2/3 rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2">
                            </div>
                            <div class="flex items-center">
                                <label for="kinerja" class="w-1/3 text-sm text-gray-600">Kinerja</label>
                                <select id="kinerja" class="w-2/3 rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2">
                                    <option>Sedang</option>
                                    <option>Tinggi</option>
                                    <option>Rendah</option>
                                </select>
                            </div>
                            <div class="flex items-center">
                                <label for="inisiatif" class="w-1/3 text-sm text-gray-600">Inisiatif</label>
                                <select id="inisiatif" class="w-2/3 rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2">
                                    <option>Sedang</option>
                                    <option>Tinggi</option>
                                    <option>Rendah</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="total-skor" class="block text-sm font-medium text-gray-700">Total Skor</label>
                        <input type="text" id="total-skor" value="84.5" disabled class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 px-4 py-2">
                    </div>
                    <div class="flex justify-end space-x-4 mt-6">
                        <button id="form-cancel-button" type="button" class="py-2 px-6 rounded-md bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 transition-colors duration-300">
                            Hapus
                        </button>
                        <button type="submit" class="py-2 px-6 rounded-md bg-navy text-white font-semibold hover:bg-baby-blue transition-colors duration-300">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>

            <div id="delete-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="bg-white p-6 rounded-lg shadow-xl text-center">
                    <p class="text-lg font-semibold text-gray-800 mb-4">Apakah anda yakin ingin menghapus data?</p>
                    <div class="flex justify-center space-x-4">
                        <button id="modal-cancel-button" class="py-2 px-6 rounded-md bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 transition-colors duration-300">
                            Tidak
                        </button>
                        <button id="modal-confirm-button" class="py-2 px-6 rounded-md bg-navy text-white font-semibold hover:bg-baby-blue transition-colors duration-300">
                            Yakin
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const data = [
            { id: 1, name: 'Raffi Ahmad', activity: 'BEM VISIT', attendance: '84.5', performance: 'Sedang', initiative: 'Sedang' },
            { id: 2, name: 'Siti Nurhaliza', activity: 'BEM VISIT', attendance: '86', performance: 'Sedang', initiative: 'Sedang' },
            { id: 3, name: 'Budi Santoso', activity: 'BEM VISIT', attendance: '87.5', performance: 'Tinggi', initiative: 'Tinggi' },
        ];

        const emptyState = document.getElementById('empty-state');
        const tableState = document.getElementById('table-state');
        const formState = document.getElementById('form-state');
        const deleteModal = document.getElementById('delete-modal');
        const pageTitle = document.getElementById('page-title');
        const createButton = document.getElementById('create-button');
        const emptyCreateButton = document.getElementById('empty-create-button');
        const formCancelButton = document.getElementById('form-cancel-button');
        const modalCancelButton = document.getElementById('modal-cancel-button');
        const modalConfirmButton = document.getElementById('modal-confirm-button');
        const tableBody = document.getElementById('table-body');

        // Fungsi untuk menampilkan data di tabel
        function renderTable() {
            tableBody.innerHTML = '';
            if (data.length > 0) {
                data.forEach((item, index) => {
                    const row = `
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${index + 1}.</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.name}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.activity}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.attendance}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.performance}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.initiative}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button data-id="${item.id}" class="text-navy hover:text-red-900 delete-button">Hapus</button>
                            </td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
                
                // Tambahkan event listener ke tombol hapus
                document.querySelectorAll('.delete-button').forEach(button => {
                    button.addEventListener('click', (e) => {
                        const id = e.target.dataset.id;
                        deleteModal.classList.remove('hidden');
                        
                        modalConfirmButton.onclick = () => {
                            // Simulasi penghapusan data
                            const index = data.findIndex(item => item.id == id);
                            if (index !== -1) {
                                data.splice(index, 1);
                                renderTable();
                            }
                            deleteModal.classList.add('hidden');
                            updateView();
                        };
                    });
                });

            }
        }
        
        // Fungsi untuk mengupdate tampilan berdasarkan data
        function updateView() {
            if (data.length === 0) {
                emptyState.classList.remove('hidden');
                tableState.classList.add('hidden');
                pageTitle.textContent = 'Kelola Penilaian Mahasiswa Magang';
            } else {
                emptyState.classList.add('hidden');
                tableState.classList.remove('hidden');
                pageTitle.textContent = 'Kelola Penilaian Mahasiswa Magang';
                renderTable();
            }
            formState.classList.add('hidden');
            createButton.classList.remove('hidden');
        }

        // Tampilkan tampilan awal saat halaman dimuat
        document.addEventListener('DOMContentLoaded', () => {
            updateView();
        });

        // Event listeners untuk tombol
        createButton.addEventListener('click', () => {
            emptyState.classList.add('hidden');
            tableState.classList.add('hidden');
            formState.classList.remove('hidden');
            createButton.classList.add('hidden');
            pageTitle.textContent = 'Form Penilaian';
        });

        emptyCreateButton.addEventListener('click', () => {
            createButton.click();
        });

        formCancelButton.addEventListener('click', () => {
            updateView();
        });

        modalCancelButton.addEventListener('click', () => {
            deleteModal.classList.add('hidden');
        });

    </script>
@endsection