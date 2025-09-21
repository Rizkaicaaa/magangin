@php
$pendaftaran = $pendaftar->pendaftaran->first();
@endphp

<div class="space-y-6">
    <!-- Informasi Pribadi -->
    <div class="border-b pb-4">
        <h4 class="text-lg font-semibold text-gray-800 mb-3">Informasi Pribadi</h4>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium text-gray-600">Nama Lengkap:</label>
                <p class="text-sm text-gray-900">{{ $pendaftar->nama_lengkap }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-600">NIM:</label>
                <p class="text-sm text-gray-900">{{ $pendaftar->nim }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-600">Email:</label>
                <p class="text-sm text-gray-900">{{ $pendaftar->email }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-600">No. Telepon:</label>
                <p class="text-sm text-gray-900">{{ $pendaftar->no_telp }}</p>
            </div>
        </div>
    </div>

    @if($pendaftaran)
    <!-- Informasi Pendaftaran -->
    <div class="border-b pb-4">
        <h4 class="text-lg font-semibold text-gray-800 mb-3">Informasi Pendaftaran</h4>
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="text-sm font-medium text-gray-600">Periode OR:</label>
                <p class="text-sm text-gray-900">{{ $pendaftaran->infoOr->periode ?? '-' }}</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-600">Pilihan Dinas 1:</label>
                    <p class="text-sm text-gray-900">{{ $pendaftaran->dinasPilihan1->nama_dinas ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600">Pilihan Dinas 2:</label>
                    <p class="text-sm text-gray-900">{{ $pendaftaran->dinasPilihan2->nama_dinas ?? '-' }}</p>
                </div>
            </div>
            @if($pendaftaran->dinasDiterima)
            <div>
                <label class="text-sm font-medium text-gray-600">Dinas Diterima:</label>
                <p class="text-sm text-green-600 font-semibold">{{ $pendaftaran->dinasDiterima->nama_dinas }}</p>
            </div>
            @endif
            <div>
                <label class="text-sm font-medium text-gray-600">Status :</label>
                @php
                $status = $pendaftaran->status_pendaftaran ?? 'terdaftar';
                $statusColors = [
                'terdaftar' => 'text-blue-600',
                'lulus_wawancara' => 'text-green-600',
                'tidak_lulus_wawancara' => 'text-red-600',
                'lulus_magang' => 'text-emerald-600',
                'tidak_lulus_magang' => 'text-orange-600'
                ];
                $statusLabels = [
                'terdaftar' => 'Terdaftar',
                'lulus_wawancara' => 'Lulus Wawancara',
                'tidak_lulus_wawancara' => 'Tidak Lulus Wawancara',
                'lulus_magang' => 'Lulus Magang',
                'tidak_lulus_magang' => 'Tidak Lulus Magang'
                ];
                @endphp
                <p class="text-sm font-semibold {{ $statusColors[$status] ?? 'text-gray-600' }}">
                    {{ $statusLabels[$status] ?? ucfirst($status) }}
                </p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-600">Tanggal Daftar:</label>
                <p class="text-sm text-gray-900">
                    {{ \Carbon\Carbon::parse($pendaftaran->tanggal_daftar ?? $pendaftar->created_at)->format('d F Y H:i') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Motivasi & Pengalaman -->
    <div class="border-b pb-4">
        <h4 class="text-lg font-semibold text-gray-800 mb-3">Motivasi & Pengalaman</h4>
        <div class="space-y-3">
            <div>
                <label class="text-sm font-medium text-gray-600">Motivasi:</label>
                <p class="text-sm text-gray-900 mt-1">{{ $pendaftaran->motivasi ?: '-' }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-600">Pengalaman:</label>
                <p class="text-sm text-gray-900 mt-1">{{ $pendaftaran->pengalaman ?: '-' }}</p>
            </div>
        </div>
    </div>

    <!-- File Dokumen -->
    <div>
        <h4 class="text-lg font-semibold text-gray-800 mb-3">Dokumen</h4>
        <div class="space-y-2">
            @if($pendaftaran->file_cv)
            <div class="flex items-center justify-between bg-gray-50 p-3 rounded">
                <span class="text-sm text-gray-700">CV</span>
                <a href="{{ route('pendaftar.download-cv', $pendaftar->id) }}"
                    class="text-blue-600 hover:text-blue-800 text-sm font-medium">Download</a>
            </div>
            @endif

            @if($pendaftaran->file_transkrip)
            <div class="flex items-center justify-between bg-gray-50 p-3 rounded">
                <span class="text-sm text-gray-700">Transkrip Nilai</span>
                <a href="{{ route('pendaftar.download-transkrip', $pendaftar->id) }}"
                    class="text-blue-600 hover:text-blue-800 text-sm font-medium">Download</a>
            </div>
            @endif

            @if(!$pendaftaran->file_cv && !$pendaftaran->file_transkrip)
            <p class="text-sm text-gray-500 italic">Tidak ada dokumen yang diupload</p>
            @endif
        </div>
    </div>
    @else
    <div class="text-center py-8">
        <p class="text-gray-500">Data pendaftaran tidak ditemukan</p>
    </div>
    @endif
</div>