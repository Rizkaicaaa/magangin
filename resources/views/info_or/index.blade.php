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
            {{-- Bagian Empty State --}}
            <div id="empty-state" class="text-center p-12 {{ count($infoOrs) > 0 ? 'hidden' : '' }}">
                <p class="text-gray-500 mb-4">
                    Belum ada Info yang dimasukkan. Silakan buat Info OR dengan klik button buat di bawah untuk membuat Info OR
                </p>
                <button id="empty-create-button" class="py-2 px-4 rounded-md bg-navy text-white font-semibold hover:bg-baby-blue transition-colors duration-300">
                    + Buat Info OR
                </button>
            </div>

            {{-- Bagian Tabel Data --}}
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($info->gambar)
                                        <button class="view-image-button text-blue-500 hover:underline" 
                                                    data-gambar-url="{{ asset($info->gambar) }}"
                                                    data-judul-info="{{ $info->judul }}">
                                                Lihat Gambar
                                        </button>
                                    @else
                                        <span class="text-gray-400">Tidak ada gambar</span>
                                    @endif
                                </td>
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

            {{-- Form, Modal Hapus, dan Modal Gambar dimuat sebagai komponen terpisah --}}
        @include('info_or.components.form')
        @include('info_or.components.delete-modal')
        @include('info_or.components.image-modal')
    
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // File: resources/js/info-or.js

const emptyState = document.getElementById('empty-state');
const tableState = document.getElementById('table-state');
const formState = document.getElementById('form-state');
const pageTitle = document.getElementById('page-title');

const createButton = document.getElementById('create-button');
const emptyCreateButton = document.getElementById('empty-create-button');

// Modal Form
const formModal = document.getElementById('form-modal');
const closeFormModalButton = document.getElementById('close-form-modal');
const formCancelButton = document.getElementById('form-cancel-button');

// Modal Delete
const deleteModal = document.getElementById('delete-modal');
const modalCancelButton = document.getElementById('modal-cancel-button');
const closeForm = document.getElementById('closeForm');

// Modal Gambar
const imageModal = document.getElementById('image-modal');
const modalImage = document.getElementById('modal-image');
const modalJudulInfo = document.getElementById('modal-judul-info');
const closeImageModalButton = document.getElementById('close-image-modal');

// Fungsi untuk mengelola tampilan utama (empty state vs. tabel)
function manageView() {
    // Cek apakah ada baris data di tabel, selain baris "empty"
    const hasData = document.querySelectorAll('#table-state table tbody tr:not(:has(td[colspan]))').length > 0;
    
    if (!hasData) {
        emptyState.classList.remove('hidden');
        tableState.classList.add('hidden');
        if (createButton) {
            createButton.classList.add('hidden');
        }
    } else {
        emptyState.classList.add('hidden');
        tableState.classList.remove('hidden');
        if (createButton) {
            createButton.classList.remove('hidden');
        }
    }
}

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    manageView();

    // Event listener untuk tombol "Buat Info OR"
    if (createButton) {
        createButton.addEventListener('click', () => {
            if (formModal) {
                formModal.classList.remove('hidden');
            }
        });
    }

    // Event listener untuk tombol "Buat Info OR" di empty state
    if (emptyCreateButton) {
        emptyCreateButton.addEventListener('click', () => {
            if (formModal) {
                formModal.classList.remove('hidden');
            }
        });
    }

    // Event listener untuk tombol batal di dalam form modal
    if (formCancelButton) {
        formCancelButton.addEventListener('click', () => {
            if (formModal) {
                formModal.classList.add('hidden');
            }
        });
    }

    // Event listener untuk tombol tutup (X) di form modal
    if (closeFormModalButton) {
        closeFormModalButton.addEventListener('click', () => {
            if (formModal) {
                formModal.classList.add('hidden');
            }
        });
    }

    // Event listener untuk tombol "Tidak" di modal delete
    if (modalCancelButton) {
        modalCancelButton.addEventListener('click', () => {
            if (deleteModal) {
                deleteModal.classList.add('hidden');
            }
        });
    }
    
    // Event listener untuk tombol "Tutup" di tabel
    document.querySelectorAll('.close-button').forEach(button => {
        button.addEventListener('click', (e) => {
            const id = e.target.dataset.id;
            if (closeForm) {
                // Pastikan URL route sudah benar.
                // Jika route Laravel-nya 'info-or.tutup', maka action harusnya seperti ini.
                closeForm.action = `/kelola-info-or/${id}/tutup`; 
                if (deleteModal) {
                    deleteModal.classList.remove('hidden');
                }
            }
        });
    });

    // Event listener untuk tombol "Lihat Gambar"
        document.querySelectorAll('.view-image-button').forEach(button => {
            button.addEventListener('click', (e) => {
                // Ambil data gambar dan judul dari atribut data-*
                const imageUrl = e.target.dataset.gambarUrl;
                const judul = e.target.dataset.judulInfo;
                
                // Pastikan semua elemen ditemukan sebelum melanjutkan
                if (modalImage && modalJudulInfo && imageModal) {
                    // Tetapkan URL gambar dan judul
                    modalImage.src = imageUrl;
                    
                    // Tampilkan modal dengan menghapus kelas 'hidden'
                    imageModal.classList.remove('hidden');
                }
            });
        });

    // Event listener untuk tombol tutup (X) di modal gambar
    if (closeImageModalButton) {
        closeImageModalButton.addEventListener('click', () => {
            if (imageModal) {
                imageModal.classList.add('hidden');
                modalImage.src = '';
                modalJudulInfo.textContent = '';
            }
        });
    }
});
    </script>
@endsection