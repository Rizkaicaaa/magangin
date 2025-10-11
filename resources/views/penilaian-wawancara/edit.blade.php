@extends('layouts.app')

@section('title', 'Edit Penilaian Wawancara | MagangIn')

@section('content')
<div class="bg-white p-8 rounded-2xl shadow-lg max-w-4xl mx-auto mt-6">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Edit Penilaian Wawancara</h2>

    <form id="editForm" action="{{ route('penilaian-wawancara.update', $penilaianWawancara->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        {{-- Baris 1: Peserta & Pewawancara --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="p-4 border rounded-lg bg-gray-50 shadow-sm">
                <p class="text-gray-600 font-semibold mb-1">Pewawancara</p>
                <input type="text" class="w-full border rounded-lg p-2 bg-gray-100" 
                       value="{{ $penilaianWawancara->jadwal->pewawancara ?? '-' }}" disabled>
                <input type="hidden" name="jadwal_seleksi_id" value="{{ $penilaianWawancara->jadwal_seleksi_id }}">
            </div>

            <div class="p-4 border rounded-lg bg-gray-50 shadow-sm">
                <p class="text-gray-600 font-semibold mb-1">Peserta</p>
                <input type="text" class="w-full border rounded-lg p-2 bg-gray-100" 
                       value="{{ $penilaianWawancara->pendaftaran->user->nama_lengkap ?? '-' }}" disabled>
                <input type="hidden" name="pendaftaran_id" value="{{ $penilaianWawancara->pendaftaran_id }}">
            </div>
        </div>

        {{-- Baris 2: Nilai --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-4 border rounded-lg bg-gray-50 shadow-sm">
                <label class="block text-gray-600 font-semibold mb-1">Nilai Komunikasi</label>
                <input type="number" name="nilai_komunikasi" min="0" max="100" 
                       value="{{ $penilaianWawancara->nilai_komunikasi }}" 
                       class="w-full border rounded-lg p-2" placeholder="0-100">
            </div>

            <div class="p-4 border rounded-lg bg-gray-50 shadow-sm">
                <label class="block text-gray-600 font-semibold mb-1">Nilai Motivasi</label>
                <input type="number" name="nilai_motivasi" min="0" max="100" 
                       value="{{ $penilaianWawancara->nilai_motivasi }}" 
                       class="w-full border rounded-lg p-2" placeholder="0-100">
            </div>
            
            <div class="p-4 border rounded-lg bg-gray-50 shadow-sm">
                <label class="block text-gray-600 font-semibold mb-1">Nilai Kemampuan</label>
                <input type="number" name="nilai_kemampuan" min="0" max="100" 
                       value="{{ $penilaianWawancara->nilai_kemampuan }}" 
                       class="w-full border rounded-lg p-2" placeholder="0-100">
            </div>
        </div>

        {{-- Tombol aksi --}}
        <div class="flex justify-end gap-3 mt-4">
            <a href="{{ route('penilaian-wawancara.index') }}" 
               class="px-5 py-2 rounded-lg bg-gray-300 hover:bg-gray-400 transition">
               Batal
            </a>
            <button type="reset" 
               class="px-5 py-2 rounded-lg bg-yellow-400 text-white hover:bg-yellow-500 transition">
               Reset
            </button>
            <button type="submit" 
               class="px-5 py-2 rounded-lg bg-navy text-white hover:bg-baby-blue transition">
               Update
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const form = document.getElementById('editForm');
    const inputKKM = document.getElementById('kkm');
    const btnTerapkan = document.getElementById('btn-kkm');
    const btnEdit = document.getElementById('btn-edit-kkm');

    // Fungsi untuk disable input KKM & tombol jika KKM > 0
    function initKKMStatus(kkmValue = null){
        const kkm = kkmValue || parseFloat(inputKKM.value);
        if(!isNaN(kkm) && kkm > 0){
            inputKKM.disabled = true;
            btnTerapkan.disabled = true;
            btnTerapkan.classList.add('opacity-50','cursor-not-allowed');
            btnEdit.classList.remove('hidden');
        } else {
            inputKKM.disabled = false;
            btnTerapkan.disabled = false;
            btnTerapkan.classList.remove('opacity-50','cursor-not-allowed');
            btnEdit.classList.add('hidden');
        }
    }

    initKKMStatus(); 

    // Submit form via AJAX
    form.addEventListener('submit', function(e){
        e.preventDefault();
        const formData = new FormData(form);
        formData.append('_method','PUT'); 

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN':'{{ csrf_token() }}'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            Swal.fire('Berhasil!', data.message, 'success');

            initKKMStatus(data.kkm);

            if(typeof updateKKMTable === 'function' && data.kkm){
                updateKKMTable(data.kkm);
            }
        })
        .catch(err => console.error(err));
    });
});
</script>
@endsection
