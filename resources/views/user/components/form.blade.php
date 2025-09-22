<div id="form-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-75 hidden">
    <div class="bg-white p-6 rounded-lg shadow-xl max-w-2xl w-full mx-4 relative">
        <button id="close-form-modal" class="absolute top-2 right-4 text-gray-500 hover:text-gray-700 text-3xl font-bold">&times;</button>
        <h2 id="form-title" class="text-2xl font-bold text-gray-800 text-center mb-6"></h2>
        <form id="user-form" action="" method="POST" class="space-y-4">
            @csrf
            
            <div class="flex flex-wrap -mx-2">
                <div class="w-full md:w-1/2 px-2 space-y-4">
                    <div>
                        <label for="nama_lengkap" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2" required>
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" name="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2" required>
                    </div>
                    <div id="password-field">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" id="password" name="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2" required>
                    </div>
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                        <select id="role" name="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2" required>
                            <option value="superadmin">Super Admin</option>
                            <option value="admin">Admin</option>
                            <option value="mahasiswa">Mahasiswa</option>
                        </select>
                    </div>
                </div>

                <div class="w-full md:w-1/2 px-2 space-y-4 mt-4 md:mt-0">
                    <div>
                        <label for="nim" class="block text-sm font-medium text-gray-700">NIM</label>
                        <input type="text" id="nim" name="nim" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2">
                    </div>
                    <div>
                        <label for="no_telp" class="block text-sm font-medium text-gray-700">No. Telp</label>
                        <input type="text" id="no_telp" name="no_telp" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2">
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2" required>
                            <option value="aktif">Aktif</option>
                            <option value="non_aktif">Non-Aktif</option>
                        </select>
                    </div>
                    <div>
                        <label for="dinas_id" class="block text-sm font-medium text-gray-700">Dinas ID</label>
                        <input type="number" id="dinas_id" name="dinas_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-baby-blue focus:ring focus:ring-baby-blue focus:ring-opacity-50 px-4 py-2">
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