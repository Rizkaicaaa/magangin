@extends('layouts.app')

@section('title', 'Tambah Penilaian Wawancara | MagangIn')

@section('content')
<div class="bg-white p-10 rounded-3xl shadow-xl max-w-4xl mx-auto mt-6">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Tambah Penilaian Wawancara</h2>

    <form action="{{ route('penilaian-wawancara.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Pilih Pewawancara --}}
        <div class="bg-blue-50 p-4 rounded-2xl shadow-sm">
            <label class="block text-blue-700 font-semibold mb-2">Pilih Penilai (Pewawancara)</label>
            <select id="jadwal_seleksi" name="jadwal_seleksi_id" class="w-full border border-blue-200 rounded-xl p-3 focus:ring-2 focus:ring-blue-400 focus:outline-none" required>
                <option value="" disabled selected>-- Pilih Pewawancara --</option>
                @foreach($jadwalseleksi as $j)
                    <option value="{{ $j->id }}">{{ $j->pewawancara }}</option>
                @endforeach
            </select>
        </div>

        {{-- Pilih Peserta --}}
        <div class="bg-green-50 p-4 rounded-2xl shadow-sm">
            <label class="block text-green-700 font-semibold mb-2">Pilih Peserta</label>
            <select id="pendaftaran_id" name="pendaftaran_id" class="w-full border border-green-200 rounded-xl p-3 focus:ring-2 focus:ring-green-400 focus:outline-none" required>
                <option value="" disabled selected>-- Pilih Peserta --</option>
            </select>
        </div>

        {{-- Nilai --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-yellow-50 p-4 rounded-2xl shadow-sm">
                <label class="block text-yellow-700 font-semibold mb-2">Nilai Komunikasi</label>
                <input type="number" name="nilai_komunikasi" min="0" max="100" 
                       class="w-full border border-yellow-200 rounded-xl p-3 focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400"
                       placeholder="0-100">
            </div>
            <div class="bg-orange-50 p-4 rounded-2xl shadow-sm">
                <label class="block text-orange-700 font-semibold mb-2">Nilai Motivasi</label>
                <input type="number" name="nilai_motivasi" min="0" max="100" 
                       class="w-full border border-orange-200 rounded-xl p-3 focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                       placeholder="0-100">
            </div>
            <div class="bg-purple-50 p-4 rounded-2xl shadow-sm">
                <label class="block text-purple-700 font-semibold mb-2">Nilai Kemampuan</label>
                <input type="number" name="nilai_kemampuan" min="0" max="100" 
                       class="w-full border border-purple-200 rounded-xl p-3 focus:ring-2 focus:ring-purple-400 focus:border-purple-400"
                       placeholder="0-100">
            </div>
        </div>

        {{-- Tombol --}}
        <div class="flex justify-end gap-3 mt-4">
            <a href="{{ route('penilaian-wawancara.index') }}" 
               class="px-6 py-2 rounded-2xl bg-gray-400 text-white hover:bg-gray-500 transition font-medium">Batal</a>
            <button type="reset" class="px-6 py-2 rounded-2xl bg-yellow-400 text-white hover:bg-yellow-500 transition font-medium">Reset</button>
            <button type="submit" class="px-6 py-2 rounded-2xl bg-navy text-white hover:bg-baby-blue transition font-medium">Simpan</button>
        </div>
    </form>
</div>

<script>
const jadwalSelect = document.getElementById('jadwal_seleksi');
const pesertaSelect = document.getElementById('pendaftaran_id');

const jadwalData = @json($jadwalseleksi); // semua jadwal + pendaftarannya
const penilaianExist = @json($penilaianExist);

jadwalSelect.addEventListener('change', function() {
    const selectedPewawancara = this.options[this.selectedIndex].text; // nama pewawancara yang dipilih

    // Kosongkan dropdown peserta
    pesertaSelect.innerHTML = '<option value="" disabled selected>-- Pilih Peserta --</option>';

    // Ambil semua jadwal yang punya pewawancara sama
    const jadwalPewawancara = jadwalData.filter(j => j.pewawancara === selectedPewawancara);

    // Tambahkan peserta dari jadwal tersebut ke dropdown
    jadwalPewawancara.forEach(j => {
        if(j.pendaftaran) { // pastikan ada peserta
            const option = document.createElement('option');
            option.value = j.pendaftaran.id;
            option.text = j.pendaftaran.user.nama_lengkap;
            option.disabled = penilaianExist.includes(j.pendaftaran.id);
            if(option.disabled) option.text += ' (Sudah Dinilai)';
            pesertaSelect.appendChild(option);
        }
    });
});
</script>

@endsection
