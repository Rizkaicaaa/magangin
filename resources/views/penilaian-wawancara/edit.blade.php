@extends('layouts.app')

@section('title', 'Edit Penilaian Wawancara | MagangIn')

@section('content')
<div class="bg-white p-10 rounded-3xl shadow-xl max-w-4xl mx-auto mt-6">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Edit Penilaian Wawancara</h2>

    <form id="editForm" action="{{ route('penilaian-wawancara.update', $penilaianWawancara->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Baris 1: Peserta & Pewawancara --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="p-4 rounded-2xl bg-blue-50 shadow-sm border border-blue-100">
                <p class="text-blue-700 font-semibold mb-2">Pewawancara</p>
                <input type="text" class="w-full border rounded-xl p-2 bg-blue-100" 
                       value="{{ $penilaianWawancara->jadwal->pewawancara ?? '-' }}" disabled>
                <input type="hidden" name="jadwal_seleksi_id" value="{{ $penilaianWawancara->jadwal_seleksi_id }}">
            </div>

            <div class="p-4 rounded-2xl bg-green-50 shadow-sm border border-green-100">
                <p class="text-green-700 font-semibold mb-2">Peserta</p>
                <input type="text" class="w-full border rounded-xl p-2 bg-green-100" 
                       value="{{ $penilaianWawancara->pendaftaran->user->nama_lengkap ?? '-' }}" disabled>
                <input type="hidden" name="pendaftaran_id" value="{{ $penilaianWawancara->pendaftaran_id }}">
            </div>
        </div>

        {{-- Baris 2: Nilai --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-4 rounded-2xl bg-yellow-50 shadow-sm border border-yellow-100">
                <label class="block text-yellow-700 font-semibold mb-2">Nilai Komunikasi</label>
                <input type="number" name="nilai_komunikasi" min="0" max="100" 
                       value="{{ $penilaianWawancara->nilai_komunikasi }}" 
                       class="w-full border rounded-xl p-2 focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400" 
                       placeholder="0-100">
            </div>

            <div class="p-4 rounded-2xl bg-orange-50 shadow-sm border border-orange-100">
                <label class="block text-orange-700 font-semibold mb-2">Nilai Motivasi</label>
                <input type="number" name="nilai_motivasi" min="0" max="100" 
                       value="{{ $penilaianWawancara->nilai_motivasi }}" 
                       class="w-full border rounded-xl p-2 focus:ring-2 focus:ring-orange-400 focus:border-orange-400" 
                       placeholder="0-100">
            </div>

            <div class="p-4 rounded-2xl bg-purple-50 shadow-sm border border-purple-100">
                <label class="block text-purple-700 font-semibold mb-2">Nilai Kemampuan</label>
                <input type="number" name="nilai_kemampuan" min="0" max="100" 
                       value="{{ $penilaianWawancara->nilai_kemampuan }}" 
                       class="w-full border rounded-xl p-2 focus:ring-2 focus:ring-purple-400 focus:border-purple-400" 
                       placeholder="0-100">
            </div>
        </div>

        {{-- Tombol aksi --}}
        <div class="flex justify-end gap-3 mt-4">
            <a href="{{ route('penilaian-wawancara.index') }}" 
               class="px-6 py-2 rounded-2xl bg-gray-400 text-white hover:bg-gray-500 transition font-medium">
               Batal
            </a>
            <button type="reset" 
               class="px-6 py-2 rounded-2xl bg-yellow-400 text-white hover:bg-yellow-500 transition font-medium">
               Reset
            </button>
            <button type="submit" 
               class="px-6 py-2 rounded-2xl bg-navy text-white hover:bg-baby-blue transition font-medium">
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

    form.addEventListener('submit', function(e){
        e.preventDefault();
        const formData = new FormData(form);
        formData.append('_method','PUT'); 

        fetch(form.action, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN':'{{ csrf_token() }}'},
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
