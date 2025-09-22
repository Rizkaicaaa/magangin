@extends('layouts.app')

@section('title', 'Data Pendaftar | MagangIn')

@section('content')

<div class="bg-white p-8 rounded-xl shadow-lg mx-6">
    <div class="flex justify-between items-center mb-6">
        <h1 id="page-title" class="text-3xl font-bold text-gray-800">Data Pendaftar</h1>
        <div class="flex space-x-2">
            <select id="filter-periode"
                class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-baby-blue">
                <option value="">Semua Periode</option>
                @foreach($allPeriode as $periode)
                <option value="{{ $periode->id }}">{{ $periode->periode }}</option>
                @endforeach
            </select>
            <select id="filter-status"
                class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-baby-blue">
                <option value="">Semua Status</option>
                <option value="terdaftar">Terdaftar</option>
                <option value="lulus_wawancara">Lulus Wawancara</option>
                <option value="tidak_lulus_wawancara">Tidak Lulus Wawancara</option>
                <option value="lulus_magang">Lulus Magang</option>
                <option value="tidak_lulus_magang">Tidak Lulus Magang</option>
            </select>
        </div>
    </div>

    <!-- Hidden alerts for SweetAlert -->
    @if(session('success'))
    <div id="success-message" data-message="{{ session('success') }}" style="display: none;"></div>
    @endif

    @if(session('error'))
    <div id="error-message" data-message="{{ session('error') }}" style="display: none;"></div>
    @endif

    <div id="content-container">
        <div id="empty-state" class="text-center p-12 {{ count($pendaftars) > 0 ? 'hidden' : '' }}">
            <p class="text-gray-500 mb-4">
                Belum ada data pendaftar
            </p>
        </div>

        <div id="table-state" class="{{ count($pendaftars) > 0 ? '' : 'hidden' }} overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama
                            Lengkap</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIM
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Pilihan 1</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Pilihan 2</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dinas
                            Diterima</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pendaftars as $pendaftar)
                    @php
                    $pendaftaran = $pendaftar->pendaftaran->first();
                    @endphp
                    <tr data-status="{{ $pendaftaran->status_pendaftaran ?? 'terdaftar' }}"
                        data-periode="{{ $pendaftaran->info_or_id ?? '' }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $loop->iteration }}.
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $pendaftar->nama_lengkap }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $pendaftar->nim }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $pendaftar->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $pendaftaran->dinasPilihan1->nama_dinas ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $pendaftaran->dinasPilihan2->nama_dinas ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                            $status = $pendaftaran->status_pendaftaran ?? 'terdaftar';
                            $statusColors = [
                            'terdaftar' => 'bg-blue-100 text-blue-800',
                            'lulus_wawancara' => 'bg-green-100 text-green-800',
                            'tidak_lulus_wawancara' => 'bg-red-100 text-red-800',
                            'lulus_magang' => 'bg-emerald-100 text-emerald-800',
                            'tidak_lulus_magang' => 'bg-orange-100 text-orange-800'
                            ];
                            $statusLabels = [
                            'terdaftar' => 'Terdaftar',
                            'lulus_wawancara' => 'Lulus Wawancara',
                            'tidak_lulus_wawancara' => 'Tidak Lulus Wawancara',
                            'lulus_magang' => 'Lulus Magang',
                            'tidak_lulus_magang' => 'Tidak Lulus Magang'
                            ];
                            @endphp
                            <div class="flex items-center justify-between">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusLabels[$status] ?? ucfirst($status) }}
                                </span>
                                <button onclick="showStatusModal({{ $pendaftar->id }}, '{{ $status }}')"
                                    class="text-purple-600 hover:text-purple-900 p-1 ml-2" title="Edit Status">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    @if($pendaftaran && $pendaftaran->dinasDiterima)
                                    <span
                                        class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                        {{ $pendaftaran->dinasDiterima->nama_dinas }}
                                    </span>
                                    @else
                                    <span class="text-gray-400 italic">Belum ditetapkan</span>
                                    @endif
                                </div>

                                @if($status === 'lulus_wawancara')
                                <button
                                    onclick="showDinasModal({{ $pendaftar->id }}, {{ $pendaftaran->dinas_diterima_id ?? 'null' }}, '{{ addslashes($pendaftaran->dinasPilihan1->nama_dinas ?? '') }}', '{{ addslashes($pendaftaran->dinasPilihan2->nama_dinas ?? '') }}', {{ $pendaftaran->pilihan_dinas_1 ?? 'null' }}, {{ $pendaftaran->pilihan_dinas_2 ?? 'null' }})"
                                    class="text-indigo-600 hover:text-indigo-900 p-1 ml-2" title="Edit Dinas Diterima">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <button onclick="showDetail({{ $pendaftar->id }})"
                                    class="text-navy hover:text-baby-blue p-1" title="Detail Pendaftar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>

                                @if($pendaftaran->file_cv)
                                <a href="{{ route('pendaftar.download-cv', $pendaftar->id) }}"
                                    class="text-green-600 hover:text-green-900 px-2 py-1 text-xs bg-green-100 rounded"
                                    title="Download CV">CV</a>
                                @endif

                                @if($pendaftaran->file_transkrip)
                                <a href="{{ route('pendaftar.download-transkrip', $pendaftar->id) }}"
                                    class="text-blue-600 hover:text-blue-900 px-2 py-1 text-xs bg-blue-100 rounded"
                                    title="Download Transkrip">Transkrip</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-6 py-4 text-center text-sm text-gray-500">
                            Belum ada pendaftar yang terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div id="detail-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white p-6 rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-96 overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">Detail Pendaftar</h3>
            <button onclick="closeDetailModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>
        <div id="detail-content">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div id="status-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white p-6 rounded-lg shadow-xl">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Update Status Pendaftar</h3>
        <form id="status-form" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select id="status" name="status"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50">
                    <option value="terdaftar">Terdaftar</option>
                    <option value="lulus_wawancara">Lulus Wawancara</option>
                    <option value="tidak_lulus_wawancara">Tidak Lulus Wawancara</option>
                    <option value="lulus_magang">Lulus Magang</option>
                    <option value="tidak_lulus_magang">Tidak Lulus Magang</option>
                </select>
            </div>
            <div class="flex justify-end space-x-4">
                <button type="button" onclick="closeStatusModal()"
                    class="py-2 px-4 rounded-md bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 transition-colors duration-300">
                    Batal
                </button>
                <button type="submit"
                    class="py-2 px-4 rounded-md bg-navy text-white font-semibold hover:bg-baby-blue transition-colors duration-300">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Dinas Modal -->
<div id="dinas-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white p-6 rounded-lg shadow-xl">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Tetapkan Dinas Penerima</h3>
        <form id="dinas-form" method="POST" action="">
            @csrf
            @method('POST')
            <div class="mb-4">
                <label for="dinas_diterima_id" class="block text-sm font-medium text-gray-700">Pilih Dinas</label>
                <select id="dinas_diterima_id" name="dinas_diterima_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50"
                    required>
                    <option value="">-- Pilih Dinas --</option>
                    <!-- Options will be populated by JavaScript -->
                </select>
            </div>
            <div class="flex justify-end space-x-4">
                <button type="button" onclick="closeDinasModal()"
                    class="py-2 px-4 rounded-md bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 transition-colors duration-300">
                    Batal
                </button>
                <button type="submit"
                    class="py-2 px-4 rounded-md bg-navy text-white font-semibold hover:bg-baby-blue transition-colors duration-300">
                    Tetapkan
                </button>
            </div>
        </form>
    </div>
</div>
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

// Show alerts from session
document.addEventListener('DOMContentLoaded', function() {
    const successMessage = document.getElementById('success-message');
    const errorMessage = document.getElementById('error-message');

    if (successMessage) {
        Toast.fire({
            icon: 'success',
            title: successMessage.getAttribute('data-message')
        });
    }

    if (errorMessage) {
        Toast.fire({
            icon: 'error',
            title: errorMessage.getAttribute('data-message')
        });
    }
});

// Filter by status
document.getElementById('filter-status').addEventListener('change', function() {
    filterTable();
});

// Filter by periode
document.getElementById('filter-periode').addEventListener('change', function() {
    filterTable();
});

// Combined filter function
function filterTable() {
    const statusValue = document.getElementById('filter-status').value;
    const periodeValue = document.getElementById('filter-periode').value;
    const rows = document.querySelectorAll('tbody tr[data-status]');

    rows.forEach(row => {
        const status = row.getAttribute('data-status');
        const periode = row.getAttribute('data-periode');

        let showRow = true;

        // Check status filter
        if (statusValue !== '' && status !== statusValue) {
            showRow = false;
        }

        // Check periode filter
        if (periodeValue !== '' && periode !== periodeValue) {
            showRow = false;
        }

        if (showRow) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Show detail modal
function showDetail(userId) {
    fetch(`/pendaftar/${userId}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('detail-content').innerHTML = html;
            document.getElementById('detail-modal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            Toast.fire({
                icon: 'error',
                title: 'Gagal memuat detail pendaftar'
            });
        });
}

function closeDetailModal() {
    document.getElementById('detail-modal').classList.add('hidden');
}

// Show status modal
function showStatusModal(userId, currentStatus) {
    document.getElementById('status-form').action = `/pendaftar/${userId}/status`;
    document.getElementById('status').value = currentStatus;
    document.getElementById('status-modal').classList.remove('hidden');
}

function closeStatusModal() {
    document.getElementById('status-modal').classList.add('hidden');
}

// Show dinas modal with only user's choice options
function showDinasModal(userId, currentDinasId, dinas1Nama, dinas2Nama, dinas1Id, dinas2Id) {
    document.getElementById('dinas-form').action = `/pendaftar/${userId}/dinas`;

    // Clear and populate select options with only user's choices
    const dinasSelect = document.getElementById('dinas_diterima_id');
    dinasSelect.innerHTML = '<option value="">-- Pilih Dinas --</option>';

    // Add pilihan dinas 1
    if (dinas1Id && dinas1Nama && dinas1Id !== 'null') {
        const option1 = document.createElement('option');
        option1.value = dinas1Id;
        option1.textContent = dinas1Nama + ' (Pilihan 1)';
        dinasSelect.appendChild(option1);
    }

    // Add pilihan dinas 2 if exists
    if (dinas2Id && dinas2Nama && dinas2Id !== 'null') {
        const option2 = document.createElement('option');
        option2.value = dinas2Id;
        option2.textContent = dinas2Nama + ' (Pilihan 2)';
        dinasSelect.appendChild(option2);
    }

    // Set selected value if exists
    if (currentDinasId && currentDinasId !== 'null') {
        dinasSelect.value = currentDinasId;
    }

    document.getElementById('dinas-modal').classList.remove('hidden');
}

function closeDinasModal() {
    document.getElementById('dinas-modal').classList.add('hidden');
}

// Handle form submissions with SweetAlert
document.getElementById('status-form').addEventListener('submit', function(e) {
    e.preventDefault();

    Swal.fire({
        title: 'Konfirmasi',
        text: 'Apakah Anda yakin ingin mengubah status pendaftar?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Ubah!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            this.submit();
        }
    });
});

document.getElementById('dinas-form').addEventListener('submit', function(e) {
    e.preventDefault();

    Swal.fire({
        title: 'Konfirmasi',
        text: 'Apakah Anda yakin ingin menetapkan dinas penerima?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Tetapkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            this.submit();
        }
    });
});

// Close modals when clicking outside
window.addEventListener('click', function(event) {
    const detailModal = document.getElementById('detail-modal');
    const statusModal = document.getElementById('status-modal');
    const dinasModal = document.getElementById('dinas-modal');

    if (event.target === detailModal) {
        closeDetailModal();
    }
    if (event.target === statusModal) {
        closeStatusModal();
    }
    if (event.target === dinasModal) {
        closeDinasModal();
    }
});
</script>
@endsection