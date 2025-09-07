@extends('layouts.app')

@section('title', 'Kelola Info OR | MagangIn')

@section('content')
    <div class="p-8">
        <div class="bg-white p-8 rounded-xl shadow-lg max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 id="page-title" class="text-3xl font-bold text-gray-800">Kelola Info OR</h1>
                <button id="create-button" class="py-2 px-4 rounded-md bg-baby-blue text-white font-semibold hover:bg-navy transition-colors duration-300 hidden">
                    Buat Info OR
                </button>
            </div>

            <div id="content-container">
                <div id="empty-state" class="text-center p-12">
                    <p class="text-gray-500 mb-4">
                        Belum ada Info yang dimasukkan. Silakan buat Info OR dengan klik button buat di bawah untuk membuat Info OR
                    </p>
                    <button id="empty-create-button" class="py-2 px-4 rounded-md bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 transition-colors duration-300">
                        Buat Info OR
                    </button>
                </div>

                <div id="table-state" class="hidden overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul Info</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Dibuat</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="table-body" class="bg-white divide-y divide-gray-200">
                        </tbody>
                    </table>
                </div>
                
                <div id="form-state" class="hidden p-8 border-2 rounded-lg border-gray-200 max-w-lg mx-auto">
                    <h2 class="text-2xl font-bold text-gray-800 text-center mb-6">Form Info OR</h2>
                    <form class="space-y-4">
                        <div>
                            <label for="judul-info" class="block text-sm font-medium text-gray-700">Judul Info</label>
                            <input type="text" id="judul-info" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2">
                        </div>
                        <div>
                            <label for="deskripsi-info" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <textarea id="deskripsi-info" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2"></textarea>
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
                        <p class="text-lg font-semibold text-gray-800 mb-4">Apakah Anda yakin ingin menghapus data ini?</p>
                        <div class="flex justify-center space-x-4">
                            <button id="modal-cancel-button" class="py-2 px-6 rounded-md bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 transition-colors duration-300">
                                Tidak
                            </button>
                            <button id="modal-confirm-button" class="py-2 px-6 rounded-md bg-baby-blue text-white font-semibold hover:bg-navy transition-colors duration-300">
                                Yakin
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Data dummy untuk simulasi
        const dataInfoOr = [];

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
            tableBody.innerHTML = ''; // Kosongkan tabel sebelum render ulang
            if (dataInfoOr.length > 0) {
                dataInfoOr.forEach((item, index) => {
                    const row = `
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${index + 1}.</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.title}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">${item.description}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.date}</td>
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
                        const id = parseInt(e.target.dataset.id);
                        deleteModal.classList.remove('hidden');
                        
                        modalConfirmButton.onclick = () => {
                            // Simulasi penghapusan data
                            const index = dataInfoOr.findIndex(item => item.id === id);
                            if (index !== -1) {
                                dataInfoOr.splice(index, 1);
                                renderTable();
                            }
                            deleteModal.classList.add('hidden');
                            updateView(); // Perbarui tampilan setelah penghapusan
                        };
                    });
                });
            }
        }
        
        // Fungsi untuk mengupdate tampilan berdasarkan data
        function updateView() {
            if (dataInfoOr.length === 0) {
                emptyState.classList.remove('hidden');
                tableState.classList.add('hidden');
                createButton.classList.add('hidden'); // Sembunyikan tombol "Buat Info OR" di header jika kosong
                pageTitle.textContent = 'Kelola Info OR';
            } else {
                emptyState.classList.add('hidden');
                tableState.classList.remove('hidden');
                createButton.classList.remove('hidden'); // Tampilkan tombol "Buat Info OR" di header jika ada data
                pageTitle.textContent = 'Kelola Info OR'; // Judul tetap sama untuk saat ini
                renderTable();
            }
            formState.classList.add('hidden'); // Selalu sembunyikan form saat updateView
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
            createButton.classList.add('hidden'); // Sembunyikan tombol buat saat form aktif
            pageTitle.textContent = 'Form Info OR'; // Ganti judul halaman saat form aktif
        });

        emptyCreateButton.addEventListener('click', () => {
            createButton.click(); // Panggil event click dari createButton
        });

        formCancelButton.addEventListener('click', () => {
            updateView(); // Kembali ke tampilan tabel atau kosong
            pageTitle.textContent = 'Kelola Info OR'; // Kembalikan judul halaman
        });

        modalCancelButton.addEventListener('click', () => {
            deleteModal.classList.add('hidden'); // Sembunyikan modal hapus
        });
    </script>
@endsection