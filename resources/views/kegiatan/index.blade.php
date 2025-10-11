@extends('layouts.app')

@section('title', 'Jadwal Kegiatan | MagangIn')

@section('content')
<div class="bg-white p-8 rounded-xl shadow-lg mx-6">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

        @if($userRole === 'mahasiswa')
        <h1 class="text-3xl font-bold text-gray-800"> 🗓️ Jadwal Kegiatan Magang</h1>
        @elseif($userRole === 'admin')
        <h1 class="text-3xl font-bold text-gray-800">Jadwal Kegiatan Magang</h1>
        @else
        <h1 class="text-3xl font-bold text-gray-800">Kelola Jadwal Kegiatan</h1>
        @endif

        <div class="flex items-center gap-4">
            {{-- Select periode hanya ditampilkan superadmin --}}
            @if($userRole === 'superadmin')
            <div class="mb-6">
                <label for="periode-select" class="block text-sm font-medium text-gray-700 mb-2">
                    Pilih Periode:
                </label>
                <select id="periode-select"
                    class="block w-48 px-3 py-2 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">-- Pilih Periode --</option>
                    @foreach($periodes as $periode)
                    <option value="{{ $periode->id }}" data-status="{{ $periode->status }}" @if($selectedPeriode &&
                        $periode->id == $selectedPeriode) selected @endif>
                        {{ $periode->periode }}
                        @if($periode->status === 'tutup') - Tutup
                        @elseif($periode->status === 'buka') - Buka
                        @endif
                    </option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Button Tambah Kegiatan hanya untuk superadmin --}}
            @if($userRole === 'superadmin')
            <button id="open-create-modal"
                class="py-2 px-4 rounded-lg bg-navy text-white font-semibold hover:bg-baby-blue transition disabled:opacity-50 disabled:cursor-not-allowed"
                @if($userRole !=='superadmin' || !$selectedPeriode) disabled @endif>
                Tambah Kegiatan
            </button>
            @endif
        </div>
    </div>

    {{-- Empty State --}}
    <div id="empty-state" class="text-center p-12 hidden">
        @if($userRole === 'superadmin')
        <p class="text-gray-500 mb-4">
            Silahkan pilih periode untuk melihat jadwal kegiatan yang tersedia.
        </p>
        @elseif($userRole === 'admin')
        <p class="text-gray-500 mb-4">
            Maaf, belum ada jadwal kegiatan untuk periode ini.
        </p>
        @else
        <p class="text-gray-500 mb-4">
            Maaf, belum ada jadwal kegiatan untuk periode ini.
        </p>
        @endif
    </div>



    {{-- Table State --}}
    <div id="table-state" class="overflow-x-auto hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tempat</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody id="table-body" class="bg-white divide-y divide-gray-200"></tbody>
        </table>
    </div>
</div>

{{-- Modal Form (Tambah & Edit) - Hanya untuk superadmin --}}
@if($userRole === 'superadmin')
<div id="form-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6 relative">

        <button id="close-form-modal" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 transition">
            ✕
        </button>

        <h2 id="modal-title" class="text-2xl font-bold text-gray-800 mb-6">Tambah Kegiatan</h2>

        <form id="kegiatan-form" class="space-y-4">
            <input type="hidden" id="form-id" name="id">

            <input type="hidden" id="info_or_id" name="info_or_id">

            <div>
                <label for="nama_kegiatan" class="block text-sm font-medium text-gray-700">Nama Kegiatan</label>
                <input type="text" id="nama_kegiatan" name="nama_kegiatan" class="input-field"
                    placeholder="Masukkan nama kegiatan" required>
            </div>

            <div>
                <label for="deskripsi_kegiatan" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                <textarea id="deskripsi_kegiatan" name="deskripsi_kegiatan" rows="3" class="input-field"
                    placeholder="Masukkan deskripsi kegiatan"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="tanggal_kegiatan" class="block text-sm font-medium text-gray-700">Tanggal</label>
                    <input type="date" id="tanggal_kegiatan" name="tanggal_kegiatan" class="input-field" required>
                </div>
                <div>
                    <label for="tempat" class="block text-sm font-medium text-gray-700">Tempat</label>
                    <input type="text" id="tempat" name="tempat" class="input-field"
                        placeholder="Masukkan tempat kegiatan">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="waktu_mulai" class="block text-sm font-medium text-gray-700">Waktu Mulai</label>
                    <input type="time" id="waktu_mulai" name="waktu_mulai" class="input-field" required>
                </div>
                <div>
                    <label for="waktu_selesai" class="block text-sm font-medium text-gray-700">Waktu Selesai</label>
                    <input type="time" id="waktu_selesai" name="waktu_selesai" class="input-field">
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" id="cancel-form"
                    class="px-5 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-lg bg-navy text-white hover:bg-baby-blue">
                    <span id="submit-text">Simpan</span>
                    <div id="submit-loading" class="hidden inline-flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Menyimpan...
                    </div>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Delete - Hanya untuk superadmin --}}

<div id="delete-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-lg p-6 text-center w-full max-w-sm relative">

        <button id="close-delete-modal" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 transition">
            ✕
        </button>

        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
        </div>

        <h3 class="text-lg font-medium text-gray-900 mb-2">Hapus Kegiatan</h3>
        <p class="text-sm text-gray-500 mb-6">
            Apakah Anda yakin ingin menghapus kegiatan ini? Tindakan ini tidak dapat dibatalkan.
        </p>

        <form id="delete-form" class="flex justify-center gap-3">
            <button type="button" id="cancel-delete"
                class="px-4 py-2 rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300 font-medium">
                Batal
            </button>
            <button type="submit" id="confirm-delete"
                class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700 font-medium">
                <span id="delete-text">Hapus</span>
                <div id="delete-loading" class="hidden inline-flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    Menghapus...
                </div>
            </button>
        </form>
    </div>
</div>

@endif

@endsection

@section('scripts')
<!-- Sweet Alert 2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
.input-field {
    @apply mt-1 block w-full rounded-md border-gray-300 shadow-sm focus: border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2;
}

.swal2-popup {
    border-radius: 12px !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get user role from Laravel
    const userRole = @json($userRole);
    const selectedPeriode = @json($selectedPeriode);
    // Elements
    const periodeSelect = document.getElementById('periode-select');
    const addButton = document.getElementById('open-create-modal');
    const tableBody = document.getElementById('table-body');
    const emptyState = document.getElementById('empty-state');
    const tableState = document.getElementById('table-state');

    // Modal elements (hanya ada untuk superadmin)
    const formModal = document.getElementById('form-modal');
    const form = document.getElementById('kegiatan-form');
    const modalTitle = document.getElementById('modal-title');
    const formId = document.getElementById('form-id');
    const deleteModal = document.getElementById('delete-modal');

    // Variables
    let currentPeriodeId = selectedPeriode;
    let deleteId = null;
    let kegiatanData = [];


    // CSRF Token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // Sweet Alert configuration
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    // Event listener untuk perubahan periode (hanya untuk admin/superadmin)
    if (periodeSelect && userRole === 'superadmin') {
        periodeSelect.addEventListener('change', function() {
            const periodeId = this.value;
            currentPeriodeId = periodeId;

            // Clear table body
            tableBody.innerHTML = '';

            if (!periodeId) {
                if (addButton) addButton.disabled = true;
                showEmptyState('Pilih periode untuk melihat jadwal kegiatan yang tersedia.');
                return;
            }

            // Enable button dan load data
            if (addButton) addButton.disabled = false;
            loadJadwalKegiatan(periodeId);
        });

        //  Cek apakah ada periode yang sudah terpilih (status = buka) saat pertama kali load
        const selectedOption = periodeSelect.options[periodeSelect.selectedIndex];
        if (selectedOption && selectedOption.value) {
            currentPeriodeId = selectedOption.value;
            if (addButton) addButton.disabled = false;
            loadJadwalKegiatan(currentPeriodeId);
        }
    }

    if (userRole === 'admin' && selectedPeriode) {
        loadJadwalKegiatan(selectedPeriode);
    }

    // Untuk mahasiswa, langsung load data berdasarkan periode yang sudah ditentukan
    if (userRole === 'mahasiswa' && selectedPeriode) {
        loadJadwalKegiatan(selectedPeriode);
    }

    // Function untuk load jadwal kegiatan
    function loadJadwalKegiatan(periodeId) {
        console.log('Loading jadwal untuk periode:', periodeId);

        // Fetch data menggunakan route yang benar
        fetch(`/jadwal-kegiatan/api/by-periode?periode_id=${periodeId}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => {
                console.log('Response status:', response.status);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Data received:', data);

                if (data.success) {
                    kegiatanData = data.data || [];
                    renderTable();
                } else {
                    showError(data.message || 'Gagal memuat data jadwal kegiatan');
                }
            })
            .catch(error => {
                console.error('Error loading jadwal:', error);
                showError('Gagal memuat data jadwal kegiatan: ' + error.message);
            });
    }

    // Function untuk render tabel
    function renderTable() {
        tableBody.innerHTML = '';

        if (kegiatanData.length === 0) {
            let message = '';

            if (userRole === 'superadmin') {
                message =
                    'Belum ada jadwal kegiatan untuk periode ini. Klik tombol tambah untuk membuat jadwal kegiatan.';
            } else {
                // Mahasiswa dan admin sama
                message = 'Belum ada jadwal kegiatan untuk periode ini.';
            }

            showEmptyState(message);
            return;
        }


        // Show table
        emptyState.classList.add('hidden');
        tableState.classList.remove('hidden');

        kegiatanData.forEach((item, index) => {
            // Buat kolom aksi hanya untuk superadmin
            let actionColumn = `
    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
        <div class="flex justify-center space-x-2">
            <button onclick="showDetail(${item.id})" 
                    class="inline-flex items-center p-2 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded-full transition-all duration-200" title="Lihat Detail">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
            </button>
`;

            if (userRole === 'superadmin') {
                actionColumn += `
        <button onclick="editKegiatan(${item.id})" 
                class="inline-flex items-center p-2 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-full transition-all duration-200" title="Edit Kegiatan">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
        </button>
        <button onclick="deleteKegiatan(${item.id})" 
                class="inline-flex items-center p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-full transition-all duration-200" title="Hapus Kegiatan">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </button>
    `;
            }

            actionColumn += `</div></td>`;


            const row = `
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${index + 1}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${escapeHtml(item.nama_kegiatan || '-')}</td>
                    <td class="px-6 py-4 text-sm text-gray-900 max-w-xs" title="${escapeHtml(item.deskripsi_kegiatan || '')}">
                        <div class="truncate">${escapeHtml(item.deskripsi_kegiatan || '-')}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${formatDate(item.tanggal_kegiatan) || '-'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <div class="flex items-center gap-1">
                            <span class="text-green-600 font-medium">${formatTime(item.waktu_mulai)}</span>
                            ${item.waktu_mulai && item.waktu_selesai ? '<span class="text-gray-400">-</span>' : ''}
                            <span class="text-red-600 font-medium">${formatTime(item.waktu_selesai)}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${escapeHtml(item.tempat || '-')}</td>
                    ${actionColumn}
                </tr>
            `;
            tableBody.innerHTML += row;
        });
    }


    function showDetail(id) { // ✅ Harus di luar fungsi lain
        fetch(`/jadwal-kegiatan/${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const d = data.data;
                    Swal.fire({
                        title: d.nama_kegiatan,
                        html: `
                        <div class="text-left space-y-2">
                            <p><strong> Tanggal:</strong> ${d.tanggal_kegiatan_formatted}</p>
                            <p><strong> Waktu:</strong> ${d.waktu_mulai} - ${d.waktu_selesai || '-'}</p>
                            <p><strong> Tempat:</strong> ${d.tempat}</p>
                            <p><strong> Deskripsi:</strong> ${d.deskripsi_kegiatan || '-'}</p>
                            <p><strong> Periode:</strong> ${d.periode ? d.periode.periode : '-'}</p>
                        </div>
                    `,
                        confirmButtonColor: '#3b82f6',
                        icon: 'info'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Memuat Detail',
                        text: data.message || 'Data tidak ditemukan'
                    });
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: 'Tidak dapat mengambil detail kegiatan.'
                });
            });
    }

    window.showDetail = showDetail;

    // Helper function to escape HTML
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function(m) {
            return map[m];
        });
    }

    // Format date helper
    function formatDate(dateStr) {
        if (!dateStr) return '-';
        try {
            const date = new Date(dateStr);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            });
        } catch (e) {
            return dateStr;
        }
    }

    // Format time helper
    function formatTime(timeStr) {
        if (!timeStr) return '';
        try {
            // Handle both HH:MM and full datetime format
            if (timeStr.includes('T') || timeStr.length > 8) {
                return timeStr.substring(11, 16);
            }
            return timeStr.substring(0, 5);
        } catch (e) {
            return timeStr;
        }
    }

    // Utility functions
    function showEmptyState(message) {
        let addButtonHtml = '';


        emptyState.innerHTML = `
            <div class="text-center p-12">
                <div class="mx-auto h-24 w-24 text-gray-300 mb-4">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4v10m4-10v10m-4-4h4m-4-4h4m-4-8h4"/>
                    </svg>
                </div>
                <p class="text-gray-500 text-lg mb-4">${message}</p>
                ${addButtonHtml}
            </div>
        `;
        emptyState.classList.remove('hidden');
        tableState.classList.add('hidden');
    }

    function showError(message) {
        Toast.fire({
            icon: 'error',
            title: 'Error!',
            text: message
        });
    }

    function showSuccess(message) {
        Toast.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: message
        });
    }

    // Modal handlers (hanya untuk superadmin)
    if (addButton && userRole === 'superadmin') {
        addButton.addEventListener('click', function() {
            if (!currentPeriodeId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Pilih periode terlebih dahulu!',
                    confirmButtonColor: '#3b82f6'
                });
                return;
            }
            openCreateModal();
        });
    }

    function openCreateModal() {
        if (!form || userRole !== 'superadmin') return;

        form.reset();
        if (formId) formId.value = '';
        if (modalTitle) modalTitle.textContent = 'Tambah Kegiatan';

        // Set periode ID
        const periodeInput = document.getElementById('info_or_id');
        if (periodeInput) periodeInput.value = currentPeriodeId;

        // Reset submit button
        const submitText = document.getElementById('submit-text');
        const submitLoading = document.getElementById('submit-loading');
        if (submitText) submitText.classList.remove('hidden');
        if (submitLoading) submitLoading.classList.add('hidden');

        if (formModal) formModal.classList.remove('hidden');
    }

    // Make openCreateModal global untuk superadmin
    if (userRole === 'superadmin') {
        window.openCreateModal = openCreateModal;
    }

    // Form submit handler (hanya untuk superadmin)
    if (form && userRole === 'superadmin') {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            handleFormSubmit();
        });
    }

    function handleFormSubmit() {
        if (userRole !== 'superadmin') return;

        const submitButton = form.querySelector('button[type="submit"]');
        const submitText = submitButton?.querySelector('#submit-text');
        const submitLoading = submitButton?.querySelector('#submit-loading');

        // ✅ Tampilkan loading state dengan aman
        submitButton.disabled = true;
        if (submitText) submitText.classList.add('hidden');
        if (submitLoading) submitLoading.classList.remove('hidden');

        const formData = new FormData(form);
        const isEdit = formId.value !== '';
        let url = '/jadwal-kegiatan';

        if (isEdit) {
            url = `/jadwal-kegiatan/${formId.value}`;
            formData.append('_method', 'PUT');
        }

        fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (formModal) formModal.classList.add('hidden');
                    loadJadwalKegiatan(currentPeriodeId);
                    showSuccess(data.message || 'Data berhasil disimpan');
                } else {
                    if (data.errors) {
                        let errorMessage = 'Terjadi kesalahan:\n';
                        Object.values(data.errors).forEach(error => {
                            errorMessage += `• ${error[0]}\n`;
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal',
                            text: errorMessage,
                            confirmButtonColor: '#3b82f6'
                        });
                    } else {
                        showError(data.message || 'Gagal menyimpan data');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Terjadi kesalahan saat menyimpan data');
            })
            .finally(() => {
                //  Reset loading state aman
                submitButton.disabled = false;
                if (submitText) submitText.classList.remove('hidden');
                if (submitLoading) submitLoading.classList.add('hidden');
            });
    }

    // Fungsi edit (hanya untuk superadmin)
    if (userRole === 'superadmin') {
        window.editKegiatan = function(id) {
            const item = kegiatanData.find(d => d.id === id);
            if (!item || !form || !formModal) {
                console.error('❌ Modal atau form belum terload.');
                return;
            }

            formId.value = item.id;
            document.getElementById('nama_kegiatan').value = item.nama_kegiatan || '';
            document.getElementById('deskripsi_kegiatan').value = item.deskripsi_kegiatan || '';
            document.getElementById('tanggal_kegiatan').value = item.tanggal_kegiatan || '';
            document.getElementById('waktu_mulai').value = item.waktu_mulai || '';
            document.getElementById('waktu_selesai').value = item.waktu_selesai || '';
            document.getElementById('tempat').value = item.tempat || '';
            document.getElementById('info_or_id').value = item.info_or_id || currentPeriodeId;

            modalTitle.textContent = 'Edit Kegiatan';

            document.getElementById('submit-text').classList.remove('hidden');
            document.getElementById('submit-loading').classList.add('hidden');

            formModal.classList.remove('hidden');
        }

        window.deleteKegiatan = function(id) {
            deleteId = id;
            const item = kegiatanData.find(d => d.id === id);

            Swal.fire({
                title: 'Hapus Kegiatan?',
                html: `Apakah Anda yakin ingin menghapus kegiatan <strong>"${item?.nama_kegiatan || 'ini'}"</strong>?<br><span class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    performDelete(id);
                }
            });
        };
    }


    function performDelete(id) {
        if (userRole !== 'superadmin') return;

        // Show loading alert
        Swal.fire({
            title: 'Menghapus...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch(`/jadwal-kegiatan/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();

                if (data.success) {
                    loadJadwalKegiatan(currentPeriodeId);
                    showSuccess(data.message || 'Data berhasil dihapus');
                } else {
                    showError(data.message || 'Gagal menghapus data');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.close();
                showError('Terjadi kesalahan saat menghapus data');
            });
    }

    // Close modal handlers (hanya untuk superadmin)
    if (userRole === 'superadmin') {
        const closeFormModal = document.getElementById('close-form-modal');
        if (closeFormModal) {
            closeFormModal.addEventListener('click', function() {
                if (formModal) formModal.classList.add('hidden');
            });
        }

        const cancelForm = document.getElementById('cancel-form');
        if (cancelForm) {
            cancelForm.addEventListener('click', function() {
                if (formModal) formModal.classList.add('hidden');
            });
        }

        // Close modal when clicking outside
        [formModal, deleteModal].forEach(modal => {
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        modal.classList.add('hidden');
                    }
                });
            }
        });
    }

    // Initialize
    console.log('Jadwal Kegiatan script initialized for role:', userRole);
});
</script>
@endsection