@extends('layouts.app')

@section('title', 'Kelola Jadwal Kegiatan | MagangIn')

@section('content')
<div class="bg-white p-8 rounded-xl shadow-lg mx-6">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Kelola Jadwal Kegiatan</h1>
        <button id="open-create-modal" 
            class="py-2 px-4 rounded-lg bg-navy text-white font-semibold hover:bg-baby-blue transition">
            + Tambah Kegiatan
        </button>
    </div>

    {{-- Empty State --}}
    <div id="empty-state" class="text-center p-12 hidden">
        <p class="text-gray-500 mb-4">
            Belum ada data kegiatan. Klik tombol tambah untuk membuat jadwal baru.
        </p>
        <button id="empty-create-button" 
            class="py-2 px-4 rounded-md bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 transition">
            Tambah Kegiatan
        </button>
    </div>

    {{-- Table State --}}
    <div id="table-state" class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tempat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dinas</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody id="table-body" class="bg-white divide-y divide-gray-200"></tbody>
        </table>
    </div>
</div>

{{-- Modal Form (Tambah & Edit) --}}
<div id="form-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6 relative">
        
        {{-- Tombol X (close) --}}
        <button id="close-form-modal" 
            class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 transition">
            ✕
        </button>

        <h2 id="modal-title" class="text-2xl font-bold text-gray-800 mb-6">Tambah Kegiatan</h2>
        
        <form id="kegiatan-form" class="space-y-4">
            <input type="hidden" id="form-id">

            <div>
                <label for="nama-kegiatan" class="block text-sm font-medium text-gray-700">Nama Kegiatan</label>
                <input type="text" id="nama-kegiatan" class="input-field" placeholder="Masukkan nama kegiatan">
            </div>

            <div>
                <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                <textarea id="deskripsi" rows="3" class="input-field" placeholder="Masukkan deskripsi kegiatan"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700">Tanggal</label>
                    <input type="date" id="tanggal" class="input-field">
                </div>
                <div>
                    <label for="waktu" class="block text-sm font-medium text-gray-700">Waktu</label>
                    <input type="time" id="waktu" class="input-field">
                </div>
            </div>

            <div>
                <label for="tempat" class="block text-sm font-medium text-gray-700">Tempat</label>
                <input type="text" id="tempat" class="input-field" placeholder="Masukkan tempat kegiatan">
            </div>

            <div>
                <label for="dinas" class="block text-sm font-medium text-gray-700">Dinas</label>
                <select id="dinas" class="input-field">
                    <option value="">Pilih Dinas</option>
                    <option value="PSDM">PSDM</option>
                    <option value="SOSMAS">SOSMAS</option>
                    <option value="AI">AI</option>
                </select>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" id="cancel-form" class="px-5 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-lg bg-navy text-white hover:bg-baby-blue">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>


{{-- Modal Delete --}}
<div id="delete-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-lg p-6 text-center w-full max-w-sm relative">
        
       {{-- Tombol X (close) --}}
        <button id="close-delete-modal" 
            class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 transition">
            ✕
        </button>

        <p class="text-lg font-semibold text-gray-800 mb-6">
            Yakin ingin menghapus kegiatan ini?
        </p>
        <div class="flex justify-center gap-4">
            <button id="cancel-delete" class="px-5 py-2 rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300">
                Batal
            </button>
            <button id="confirm-delete" class="px-5 py-2 rounded-md bg-red-600 text-white hover:bg-red-700">
                Hapus
            </button>
        </div>
    </div>
</div>


@endsection

@section('scripts')
<style>
    .input-field {
        @apply mt-1 block w-full rounded-md border-gray-300 shadow-sm 
               focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2;
    }
</style>

<script>
    let data = [
        {id: 1, name: 'Workshop Pengembangan Karir', description: 'Pelatihan karir untuk mahasiswa', date: '2024-09-15', time: '09:00', place: 'Ruang Seminar Lt. 3', department: 'PSDM'},
        {id: 2, name: 'Bakti Sosial', description: 'Program bantuan sosial untuk masyarakat', date: '2024-09-18', time: '08:00', place: 'Desa Sumber Makmur', department: 'SOSMAS'},
        {id: 3, name: 'Seminar AI', description: 'Seminar perkembangan AI di era digital', date: '2024-09-20', time: '13:30', place: 'Auditorium Utama', department: 'AI'},
    ];

    const tableBody = document.getElementById('table-body');
    const emptyState = document.getElementById('empty-state');
    const tableState = document.getElementById('table-state');

    const formModal = document.getElementById('form-modal');
    const form = document.getElementById('kegiatan-form');
    const modalTitle = document.getElementById('modal-title');
    const formId = document.getElementById('form-id');

    const deleteModal = document.getElementById('delete-modal');
    let deleteId = null;

    // Render tabel
    function renderTable() {
        tableBody.innerHTML = '';
        if (data.length === 0) {
            emptyState.classList.remove('hidden');
            tableState.classList.add('hidden');
        } else {
            emptyState.classList.add('hidden');
            tableState.classList.remove('hidden');
            data.forEach((item, index) => {
                tableBody.innerHTML += `
                    <tr>
                        <td class="px-6 py-3 text-sm">${index+1}</td>
                        <td class="px-6 py-3 text-sm">${item.name}</td>
                        <td class="px-6 py-3 text-sm">${item.description}</td>
                        <td class="px-6 py-3 text-sm">${formatDate(item.date)}</td>
                        <td class="px-6 py-3 text-sm">${item.time} WIB</td>
                        <td class="px-6 py-3 text-sm">${item.place}</td>
                        <td class="px-6 py-3 text-sm">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold ${getDepartmentColor(item.department)}">${item.department}</span>
                        </td>
                        <td class="px-6 py-3 text-center text-sm flex justify-center gap-3">
                            <button onclick="editItem(${item.id})" class="text-blue-600 hover:text-blue-800">
                                <!-- Pencil Edit Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" 
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" 
                                        d="M16.862 3.487a2.25 2.25 0 113.182 3.182L7.5 19.313 
                                            3 21l1.687-4.5L16.862 3.487z" />
                                </svg>
                            </button>

                            <button onclick="showDelete(${item.id})" class="text-red-600 hover:text-red-800">
                                <!-- Icon Delete -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" 
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 
                                            01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 
                                            0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </td>

                    </tr>
                `;
            });
        }
    }

    function getDepartmentColor(dep) {
        switch(dep){
            case 'PSDM': return 'bg-blue-100 text-blue-800';
            case 'SOSMAS': return 'bg-green-100 text-green-800';
            case 'AI': return 'bg-purple-100 text-purple-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    }

    function formatDate(dateStr){
        return new Date(dateStr).toLocaleDateString('id-ID',{day:'2-digit', month:'long', year:'numeric'});
    }

    // Tambah data
    document.getElementById('open-create-modal').onclick = () => {
        form.reset();
        formId.value = '';
        modalTitle.textContent = 'Tambah Kegiatan';
        formModal.classList.remove('hidden');
    };
    document.getElementById('empty-create-button').onclick = () => {
        document.getElementById('open-create-modal').click();
    };

    // Edit data
    window.editItem = (id) => {
        const item = data.find(d => d.id === id);
        if(!item) return;
        formId.value = item.id;
        document.getElementById('nama-kegiatan').value = item.name;
        document.getElementById('deskripsi').value = item.description;
        document.getElementById('tanggal').value = item.date;
        document.getElementById('waktu').value = item.time;
        document.getElementById('tempat').value = item.place;
        document.getElementById('dinas').value = item.department;
        modalTitle.textContent = 'Edit Kegiatan';
        formModal.classList.remove('hidden');
    };

    // Hapus data
    window.showDelete = (id) => {
        deleteId = id;
        deleteModal.classList.remove('hidden');
    };

    document.getElementById('cancel-delete').onclick = () => {
        deleteId = null;
        deleteModal.classList.add('hidden');
    };
    document.getElementById('confirm-delete').onclick = () => {
        if(deleteId){
            data = data.filter(d => d.id !== deleteId);
            renderTable();
        }
        deleteModal.classList.add('hidden');
    };

    // Simpan (Tambah/Edit)
    form.onsubmit = (e) => {
        e.preventDefault();
        const id = formId.value;
        const item = {
            id: id ? parseInt(id) : Date.now(),
            name: document.getElementById('nama-kegiatan').value,
            description: document.getElementById('deskripsi').value,
            date: document.getElementById('tanggal').value,
            time: document.getElementById('waktu').value,
            place: document.getElementById('tempat').value,
            department: document.getElementById('dinas').value,
        };
        if(id){
            const idx = data.findIndex(d => d.id == id);
            data[idx] = item;
        } else {
            data.push(item);
        }
        formModal.classList.add('hidden');
        renderTable();
    };

    // Tutup modal form
    document.getElementById("close-form-modal").addEventListener("click", () => {
        document.getElementById("form-modal").classList.add("hidden");
    });

    // Tutup modal delete
    document.getElementById("close-delete-modal").addEventListener("click", () => {
        document.getElementById("delete-modal").classList.add("hidden");
    });

    document.getElementById('cancel-form').onclick = () => {
    formModal.classList.add('hidden');
    };

    // Init
    renderTable();
</script>
@endsection
