@extends('layouts.app')

@section('title', 'Kelulusan Wawancara | MagangIn')

@section('content')
<div class="bg-white p-8 rounded-xl shadow-lg mx-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <h1 class="text-3xl font-bold text-gray-800">🎓 Kelulusan Wawancara</h1>
    </div>

    @if($penilaian)
    <div
        class="border border-gray-200 rounded-xl p-6 bg-gradient-to-br from-blue-50 via-blue-100 to-blue-50 shadow-inner mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-gray-700">
            <div>
                <p class="font-semibold text-gray-600">Nama Peserta</p>
                <p class="text-lg font-medium">{{ $penilaian->pendaftaran->user->nama_lengkap ?? '-' }}</p>
            </div>
            <div>
                <p class="font-semibold text-gray-600">Dinas Diterima</p>
                <p class="text-lg font-medium">{{ $penilaian->pendaftaran->dinasDiterima->nama_dinas ?? '-' }}</p>
            </div>
            <div>
                <p class="font-semibold text-gray-600">Nilai Rata-rata</p>
                <p class="text-xl font-bold text-blue-700">{{ $penilaian->nilai_rata_rata ?? '-' }}</p>
            </div>
            <div>
                <p class="font-semibold text-gray-600">Status Seleksi</p>
                @if($penilaian->nilai_rata_rata >= 75)
                <span
                    class="inline-flex items-center px-3 py-1 mt-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold">
                    <i class="fas fa-check-circle mr-2"></i> Lolos
                </span>
                @else
                <span
                    class="inline-flex items-center px-3 py-1 mt-1 bg-red-100 text-red-700 rounded-full text-sm font-semibold">
                    <i class="fas fa-times-circle mr-2"></i> Tidak Lolos
                </span>
                @endif
            </div>
        </div>

        <div class="mt-8 text-center">
            <button onclick="openDetailModal()"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2.5 rounded-lg transition duration-200 shadow-md hover:shadow-lg inline-flex items-center gap-2">
                <i class="fas fa-eye"></i> Lihat Detail Nilai
            </button>
        </div>
    </div>
    @else
    <div class="text-center py-16">
        <img src="https://cdn-icons-png.flaticon.com/512/4076/4076505.png" class="w-32 mx-auto mb-4 opacity-80">
        <p class="text-gray-500 text-lg font-medium">Data hasil wawancara belum tersedia untuk akun Anda.</p>
    </div>
    @endif

    {{-- Modal Detail Nilai --}}
    <div id="detailModal"
        class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300">
        <div
            class="bg-white rounded-2xl shadow-2xl p-6 w-11/12 sm:w-full max-w-lg relative transform scale-95 transition-transform duration-300 ease-out">
            <button onclick="closeDetailModal()"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 transition">
                <i class="fas fa-times text-2xl"></i>
            </button>
            <h2 class="text-2xl font-bold text-gray-800 mb-4 text-center">📝 Detail Nilai Wawancara</h2>

            @if($penilaian)
            <div class="divide-y divide-gray-200">
                <div class="py-2 flex justify-between">
                    <span class="font-medium text-gray-600">Nilai Komunikasi</span>
                    <span class="font-semibold text-gray-800">{{ $penilaian->nilai_komunikasi }}</span>
                </div>
                <div class="py-2 flex justify-between">
                    <span class="font-medium text-gray-600">Nilai Motivasi</span>
                    <span class="font-semibold text-gray-800">{{ $penilaian->nilai_motivasi }}</span>
                </div>
                <div class="py-2 flex justify-between">
                    <span class="font-medium text-gray-600">Nilai Kemampuan</span>
                    <span class="font-semibold text-gray-800">{{ $penilaian->nilai_kemampuan }}</span>
                </div>
                <div class="py-2 flex justify-between">
                    <span class="font-medium text-gray-600">Nilai Total</span>
                    <span class="font-semibold text-gray-800">{{ $penilaian->nilai_total }}</span>
                </div>
                <div class="py-2 flex justify-between">
                    <span class="font-medium text-gray-600">Nilai Rata-rata</span>
                    <span class="font-semibold text-blue-700">{{ $penilaian->nilai_rata_rata }}</span>
                </div>
            </div>
            @endif

            <div class="mt-6 text-center">
                <button onclick="closeDetailModal()"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-5 py-2 rounded-lg font-medium transition duration-200">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const modal = document.getElementById('detailModal');

function openDetailModal() {
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.querySelector('div').classList.remove('scale-95');
        modal.querySelector('div').classList.add('scale-100');
    }, 10);
    modal.classList.add('flex');
}

function closeDetailModal() {
    modal.querySelector('div').classList.remove('scale-100');
    modal.querySelector('div').classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 200);
}
</script>
@endsection