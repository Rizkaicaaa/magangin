@extends('layouts.app')

@section('title', 'Edit Jadwal Wawancara | MagangIn')

@section('content')
<div class="bg-white p-8 rounded-xl shadow-lg mx-6">
    <h2 class="text-xl font-bold mb-4">Edit Jadwal Wawancara</h2>

    <form action="{{ route('jadwal-seleksi.update', $jadwalSeleksi->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700">Pilih Info OR</label>
            <select name="info_or_id" class="w-full border rounded-lg p-2">
    @foreach ($infos as $info)
        <option value="{{ $info->id }}" {{ $jadwalSeleksi->info_or_id == $info->id ? 'selected' : '' }}>
            {{ $info->judul }}
        </option>
    @endforeach
</select>

        </div>

        <div>
    <label class="block text-sm font-medium text-gray-700">Tanggal Seleksi</label>
    <input type="date" name="tanggal_seleksi" 
           value="{{ \Carbon\Carbon::parse($jadwalSeleksi->tanggal_seleksi)->format('Y-m-d') }}" 
           class="w-full border rounded-lg p-2" required>
</div>

<div class="flex gap-4">
    <div class="w-1/2">
        <label class="block text-sm font-medium text-gray-700">Waktu Mulai</label>
        <input type="time" name="waktu_mulai" 
               value="{{ \Carbon\Carbon::parse($jadwalSeleksi->waktu_mulai)->format('H:i') }}" 
               class="w-full border rounded-lg p-2" required>
    </div>
    <div class="w-1/2">
        <label class="block text-sm font-medium text-gray-700">Waktu Selesai</label>
        <input type="time" name="waktu_selesai" 
               value="{{ \Carbon\Carbon::parse($jadwalSeleksi->waktu_selesai)->format('H:i') }}" 
               class="w-full border rounded-lg p-2" required>
    </div>
</div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Tempat</label>
            <input type="text" name="tempat" value="{{ $jadwalSeleksi->tempat }}" 
                   class="w-full border rounded-lg p-2" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Nama Pewawancara</label>
            <input type="text" name="pewawancara" value="{{ $jadwalSeleksi->pewawancara }}" 
                   class="w-full border rounded-lg p-2" required>
        </div>

        <div>
    <label class="block text-sm font-medium text-gray-700">Pilih Peserta Wawancara</label>
    <div class="border rounded-lg p-3 max-h-64 overflow-y-auto">
        @foreach($pendaftarans as $p)
            <div class="flex items-center mb-2">
                <input 
                    type="checkbox" 
                    name="pendaftaran_id[]" 
                    value="{{ $p->id }}" 
                    id="p{{ $p->id }}"
                    {{ in_array($p->id, $jadwalSeleksi->pendaftarans->pluck('id')->toArray()) ? 'checked' : '' }}
                    class="mr-2"
                >
                <label for="p{{ $p->id }}">
                    {{ $p->user->name ?? 'Pendaftar #' . $p->id }}
                    ({{ $p->infoOr->judul ?? '-' }})
                </label>
            </div>
        @endforeach
    </div>
</div>


        <div class="flex justify-end gap-2 mt-4">
            <a href="{{ route('jadwal-seleksi.index') }}" 
               class="px-4 py-2 rounded-md bg-gray-300 hover:bg-gray-400">Batal</a>
            <button type="submit" class="px-4 py-2 rounded-md bg-navy text-white hover:bg-baby-blue">
                Update
            </button>
        </div>
    </form>
</div>
@endsection
