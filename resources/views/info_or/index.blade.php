@extends('layouts.app')

@section('title', 'Kelola Info OR | MagangIn')

@section('content')

{{-- Perbaikan: Pindahkan @section('content') di luar, jika tidak akan ada error --}}
@php
// Asumsi $isInfoOpen dikirim dari controller
$isInfoOpen = $isInfoOpen ?? false; // Fallback jika variabel tidak ada
$buttonDisabled = $isInfoOpen ? 'opacity-50 cursor-not-allowed' : 'hover:bg-baby-blue';
$buttonAttributes = $isInfoOpen ? 'disabled' : '';
@endphp

<div class="bg-white p-8 rounded-xl shadow-lg mx-6">
    <div class="flex justify-between items-center mb-6">
        <h1 id="page-title" class="text-3xl font-bold text-gray-800">Kelola Info OR</h1>

        {{-- Tombol Buat Info OR --}}
        <button id="create-button"
            class="py-2 px-4 rounded-md bg-navy text-white font-semibold transition-colors duration-300 {{ $buttonDisabled }} {{ count($infoOrs) > 0 ? '' : 'hidden' }}"
            {{ $buttonAttributes }}>
            + Buat Info OR
        </button>
    </div>

    @if ($isInfoOpen)
    {{-- Pesan Peringatan Saat Info OR Sedang Buka --}}
    <div id="open-info-alert" class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6 rounded-lg"
        role="alert">
        <p class="font-bold">Info OR Sedang Aktif</p>
        <p class="text-sm">Anda hanya dapat membuat Info OR baru setelah Info OR yang saat ini berstatus "buka" ditutup.
        </p>
    </div>
    @endif

    <div id="content-container">
        {{-- Bagian Empty State --}}
        <div id="empty-state" class="text-center p-12 {{ count($infoOrs) > 0 ? 'hidden' : '' }}">
            <p class="text-gray-500 mb-4">
                Belum ada Info yang dimasukkan. Silakan buat Info OR dengan klik button buat di bawah untuk membuat Info
                OR
            </p>
            <button id="empty-create-button"
                class="py-2 px-4 rounded-md bg-navy text-white font-semibold transition-colors duration-300 {{ $buttonDisabled }}"
                {{ $buttonAttributes }}>
                Buat Info OR
            </button>
        </div>

        {{-- Bagian Tabel Data --}}
        <div id="table-state" class="{{ count($infoOrs) > 0 ? '' : 'hidden' }} overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul
                            Info</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Deskripsi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Persyaratan Umum</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal Buka</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal Tutup</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Periode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Gambar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($infoOrs as $info)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $loop->iteration }}.</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $info->judul }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ Str::limit($info->deskripsi, 30) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ Str::limit($info->persyaratan_umum, 30) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($info->tanggal_buka)->format('d F Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($info->tanggal_tutup)->format('d F Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $info->periode }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($info->gambar)
                            <button class="view-image-button text-blue-500 hover:underline font-medium"
                                data-gambar-url="{{ asset($info->gambar) }}" data-judul-info="{{ $info->judul }}">
                                Lihat Gambar
                            </button>
                            @else
                            <span class="text-gray-400">Tidak ada gambar</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $info->status == 'buka' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($info->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center">
                                @if($info->status == 'buka')
                                {{-- Tombol TUTUP (Icon) --}}
                                <button data-id="{{ $info->id }}" title="Tutup Pendaftaran"
                                    class="close-button inline-flex items-center p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-full transition-all duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </button>
                                @else
                                <span class="text-gray-400">Ditutup</span>
                                @endif
                            </div>
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
// const formState = document.getElementById('form-state'); // Dihapus karena tidak digunakan
const pageTitle = document.getElementById('page-title');

const createButton = document.getElementById('create-button');
const emptyCreateButton = document.getElementById('empty-create-button');

const isInfoOpen = @json($isInfoOpen); // Variabel dari Blade

// Modal Form
const formModal = document.getElementById('form-modal');
const closeFormModalButton = document.getElementById('close-form-modal');
const formCancelButton = document.getElementById('form-cancel-button');

// Modal Delete (digunakan untuk konfirmasi TUTUP)
const deleteModal = document.getElementById('delete-modal');
const modalCancelButton = document.getElementById('modal-cancel-button');
const closeForm = document.getElementById('closeForm'); // Form di dalam modal delete

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

// Fungsi untuk membuka modal form hanya jika info sedang tidak buka
function openFormModal() {
    if (isInfoOpen) {
        // Tampilkan peringatan jika sedang ada Info OR yang buka
        Swal.fire({
            icon: 'warning',
            title: 'Pendaftaran Sedang Aktif',
            text: 'Anda harus menutup Info OR yang sedang berjalan sebelum membuat yang baru.',
            confirmButtonColor: '#3b82f6'
        });
        return;
    }
    if (formModal) {
        // Logika untuk menampilkan form modal CREATE
        formModal.classList.remove('hidden');
    }
}


// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    manageView();

    // Event listener untuk tombol "Buat Info OR" (sekarang memanggil openFormModal)
    if (createButton) {
        createButton.addEventListener('click', openFormModal);
    }

    // Event listener untuk tombol "Buat Info OR" di empty state (sekarang memanggil openFormModal)
    if (emptyCreateButton) {
        emptyCreateButton.addEventListener('click', openFormModal);
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

    // Event listener untuk tombol "Tutup" (Icon) di tabel
    document.querySelectorAll('.close-button').forEach(button => {
        button.addEventListener('click', (e) => {
            const id = e.currentTarget.dataset.id; // Menggunakan currentTarget untuk button

            // Mengganti konfirmasi delete modal dengan SweetAlert untuk konsistensi UI
            Swal.fire({
                title: 'Tutup Pendaftaran?',
                text: "Anda akan menutup pendaftaran Info OR ini. Setelah ditutup, pendaftaran tidak dapat dibuka lagi.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Tutup!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Lanjutkan dengan proses penutupan (submit form)
                    if (closeForm) {
                        closeForm.action = `/kelola-info-or/${id}/tutup`;
                        closeForm.submit(); // Submit form untuk melakukan request PUT
                    }
                }
            });
        });
    });

    // Event listener untuk tombol "Lihat Gambar"
    document.querySelectorAll('.view-image-button').forEach(button => {
        button.addEventListener('click', (e) => {
            // Ambil data gambar dan judul dari atribut data-*
            const imageUrl = e.currentTarget.dataset.gambarUrl;
            const judul = e.currentTarget.dataset.judulInfo;

            // Pastikan semua elemen ditemukan sebelum melanjutkan
            if (modalImage && modalJudulInfo && imageModal) {
                // Tetapkan URL gambar dan judul
                modalImage.src = imageUrl;
                modalJudulInfo.textContent = judul; // Menambahkan judul di modal

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