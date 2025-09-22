<div id="form-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-75 hidden">
    <div class="bg-white p-6 rounded-lg shadow-xl max-w-2xl w-full mx-4 relative">
        <button id="close-form-modal" class="absolute top-2 right-4 text-gray-500 hover:text-gray-700 text-3xl font-bold">&times;</button>
        <h2 class="text-2xl font-bold text-gray-800 text-center mb-6">Form Info OR</h2>
        <form action="{{ route('info-or.store') }}" enctype="multipart/form-data" method="POST" class="space-y-4">
            @csrf
            <div class="flex flex-wrap -mx-2">
                <div class="w-full md:w-1/2 px-2 space-y-4">
                    <div>
                        <label for="judul-info" class="block text-sm font-medium text-gray-700">Judul Info</label>
                        <input type="text" id="judul-info" name="judul" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2" required>
                    </div>
                    <div>
                        <label for="deskripsi-info" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea id="deskripsi-info" name="deskripsi" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2" required></textarea>
                    </div>
                    <div>
                        <label for="persyaratan-umum" class="block text-sm font-medium text-gray-700">Persyaratan Umum</label>
                        <textarea id="persyaratan-umum" name="persyaratan_umum" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2"></textarea>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2">
                            <option value="buka">Buka</option>
                            <option value="tutup">Tutup</option>
                        </select>
                    </div>
                </div>

                <div class="w-full md:w-1/2 px-2 space-y-4 mt-4 md:mt-0">
                    <div>
                        <label for="tanggal-buka" class="block text-sm font-medium text-gray-700">Tanggal Buka</label>
                        <input type="date" id="tanggal-buka" name="tanggal_buka" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2">
                    </div>
                    <div>
                        <label for="tanggal-tutup" class="block text-sm font-medium text-gray-700">Tanggal Tutup</label>
                        <input type="date" id="tanggal-tutup" name="tanggal_tutup" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2">
                    </div>
                    <div>
                        <label for="periode" class="block text-sm font-medium text-gray-700">Periode</label>
                        <select id="periode" name="periode" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2">
                            @php
                                $currentYear = date('Y');
                                for ($i = 0; $i < 5; $i++) {
                                    $year = $currentYear + $i;
                                    echo "<option value=\"$year\">$year</option>";
                                }
                            @endphp
                        </select>
                    </div>
                    <div>
                        <label for="gambar" class="block text-sm font-medium text-gray-700">Gambar</label>
                        <input type="file" id="gambar" name="gambar" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2">
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-4 mt-6">
                <button id="form-cancel-button" type="button" class="py-2 px-6 rounded-md bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 transition-colors duration-300">
                    Batal
                </button>
                <button type="submit" class="py-2 px-6 rounded-md bg-navy text-white font-semibold hover:bg-baby-blue transition-colors duration-300">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>