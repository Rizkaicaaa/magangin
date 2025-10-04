<!-- @extends('layouts.app')

@section('content')
<div x-data="{ openModal: false, selected: null }" class="bg-white p-8 rounded-xl shadow-lg mx-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Hasil Wawancara</h1>
    </div>

    <div id="content-container">
        <table class="w-full border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="py-3 px-4">No</th>
                    <th class="py-3 px-4">Nama Peserta</th>
                    <th class="py-3 px-4">Nilai Rata-rata</th>
                    <th class="py-3 px-4">Status Seleksi</th>
                    <th class="py-3 px-4">Dinas Diterima</th>
                    <th class="py-3 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($penilaians as $index => $penilaian)
                    <tr class="border-t hover:bg-gray-50 transition">
                        <td class="py-3 px-4">{{ $index + 1 }}</td>
                        <td class="py-3 px-4">{{ $penilaian->pendaftaran->user->nama_lengkap ?? '-' }}</td>
                        <td class="py-3 px-4">{{ $penilaian->nilai_rata_rata ?? '-' }}</td>
                        <td class="py-3 px-4">
                            @if($penilaian->nilai_rata_rata >= 75)
                                <span class="text-green-600 font-semibold">Lolos</span>
                            @else
                                <span class="text-red-600 font-semibold">Tidak Lolos</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">{{ $penilaian->pendaftaran->dinasDiterima->nama_dinas ?? '-' }}</td>
                        <td class="py-3 px-4 text-center">
                            <button 
                                @click="openModal = true; selected = {{ $penilaian->toJson() }}"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium transition-all duration-200">
                                Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-6 text-center text-gray-500">
                            Belum ada data penilaian wawancara.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Pop-up --}}
    <div 
        x-show="openModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        
        <div class="bg-white w-full max-w-2xl rounded-2xl shadow-xl p-8 relative">
            <button @click="openModal = false" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-lg"></i>
            </button>

            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Detail Hasil Wawancara</h2>

            <template x-if="selected">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-gray-700">
                    <div>
                        <p class="font-semibold">Nama Peserta</p>
                        <p x-text="selected.pendaftaran?.user?.nama_lengkap ?? '-'"></p>
                    </div>
                    <div>
                        <p class="font-semibold">Dinas Diterima</p>
                        <p x-text="selected.pendaftaran?.dinas_diterima?.nama_dinas ?? '-'"></p>
                    </div>
                    <div>
                        <p class="font-semibold">Nilai Komunikasi</p>
                        <p x-text="selected.nilai_komunikasi"></p>
                    </div>
                    <div>
                        <p class="font-semibold">Nilai Motivasi</p>
                        <p x-text="selected.nilai_motivasi"></p>
                    </div>
                    <div>
                        <p class="font-semibold">Nilai Kemampuan</p>
                        <p x-text="selected.nilai_kemampuan"></p>
                    </div>
                    <div>
                        <p class="font-semibold">Nilai Total</p>
                        <p x-text="selected.nilai_total"></p>
                    </div>
                    <div>
                        <p class="font-semibold">Nilai Rata-rata</p>
                        <p x-text="selected.nilai_rata_rata"></p>
                    </div>
                    <div>
                        <p class="font-semibold">Status Seleksi</p>
                        <template x-if="selected.nilai_rata_rata >= 75">
                            <span class="text-green-600 font-semibold">Lolos</span>
                        </template>
                        <template x-if="selected.nilai_rata_rata < 75">
                            <span class="text-red-600 font-semibold">Tidak Lolos</span>
                        </template>
                    </div>
                </div>
            </template>

            <div class="mt-8 text-right">
                <button @click="openModal = false"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium px-4 py-2 rounded-lg transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection -->
