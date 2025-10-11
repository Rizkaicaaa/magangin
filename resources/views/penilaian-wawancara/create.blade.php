@extends('layouts.app')

@section('title', 'Tambah Penilaian Wawancara | MagangIn')

@section('content')
<div class="bg-white p-8 rounded-2xl shadow-lg max-w-4xl mx-auto mt-6">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Tambah Penilaian Wawancara</h2>

    <form action="{{ route('penilaian-wawancara.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Pilih Pewawancara --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih Penilai (Pewawancara)</label>
            <select id="jadwal_seleksi" name="jadwal_seleksi_id" class="w-full ..." required>
                <option value="" disabled selected>-- Pilih Pewawancara --</option>
                @foreach($jadwalseleksi as $j)
                    <option value="{{ $j->id }}">{{ $j->pewawancara }}</option>
                @endforeach
            </select>
        </div>

        {{-- Pilih Peserta --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih Peserta</label>
            <select id="pendaftaran_id" name="pendaftaran_id" class="w-full ..." required>
                <option value="" disabled selected>-- Pilih Peserta --</option>
            </select>
        </div>

        {{-- Nilai --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nilai Komunikasi</label>
                <input type="number" name="nilai_komunikasi" min="0" max="100" 
                       class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                       placeholder="0-100">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nilai Motivasi</label>
                <input type="number" name="nilai_motivasi" min="0" max="100" 
                       class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                       placeholder="0-100">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nilai Kemampuan</label>
                <input type="number" name="nilai_kemampuan" min="0" max="100" 
                       class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                       placeholder="0-100">
            </div>
        </div>

        {{-- Tombol --}}
        <div class="flex justify-end gap-3 mt-4">
            <a href="{{ route('penilaian-wawancara.index') }}" 
               class="px-5 py-2 rounded-lg bg-gray-300 hover:bg-gray-400 transition">Batal</a>
            <button type="reset" class="px-5 py-2 rounded-lg bg-yellow-400 text-white hover:bg-yellow-500 transition">Reset</button>
            <button type="submit" class="px-5 py-2 rounded-lg bg-navy text-white hover:bg-baby-blue transition">Simpan</button>
        </div>
    </form>
</div>
@endsection

<script>
const jadwalSelect = document.getElementById('jadwal_seleksi');
const pesertaSelect = document.getElementById('pendaftaran_id');

const jadwalData = @json($jadwalseleksi); // semua jadwal + pendaftarannya

jadwalSelect.addEventListener('change', function() {
    const selectedId = this.value;
    const selectedJadwal = jadwalData.find(j => j.id == selectedId);

    // Kosongkan dropdown peserta
    pesertaSelect.innerHTML = '<option value="" disabled selected>-- Pilih Peserta --</option>';

    if (selectedJadwal && selectedJadwal.pendaftaran) {
        const pendaftaran = selectedJadwal.pendaftaran;
        pendaftaran.forEach(p => {
            const disabled = @json($penilaianExist).includes(p.id) ? 'disabled' : '';
            const option = document.createElement('option');
            option.value = p.id;
            option.text = p.user.nama_lengkap + (disabled ? ' (Sudah Dinilai)' : '');
            option.disabled = disabled;
            pesertaSelect.appendChild(option);
        });
    }
});
</script>
