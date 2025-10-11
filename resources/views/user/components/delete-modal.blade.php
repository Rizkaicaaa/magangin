<div id="delete-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white p-6 rounded-lg shadow-xl text-center max-w-sm w-full">
        <p class="text-lg font-semibold text-gray-800 mb-4">Apakah Anda yakin ingin menghapus data ini?</p>
        <p class="text-sm text-gray-600 mb-6">Tindakan ini tidak dapat diurungkan.</p>
        <div class="flex justify-center space-x-4">
            <button id="modal-cancel-button" 
                    class="py-2 px-6 rounded-md bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 transition-colors duration-300">
                Batal
            </button>
            <form id="deleteForm" method="POST" action="">
                @csrf
                <button type="submit" 
                        class="py-2 px-6 rounded-md bg-red-600 text-white font-semibold hover:bg-red-700 transition-colors duration-300">
                    Hapus
                </button>
            </form>
        </div>
    </div>
</div>