<div id="delete-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white p-6 rounded-lg shadow-xl text-center">
        <p class="text-lg font-semibold text-gray-800 mb-4">Apakah Anda yakin ingin menutup pendaftaran ini?</p>
        <div class="flex justify-center space-x-4">
            <button id="modal-cancel-button" type="button" class="py-2 px-6 rounded-md bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 transition-colors duration-300">
                Tidak
            </button>
            <form id="closeForm" method="POST" action="">
                @csrf
                @method('PUT')
                <button type="submit" class="py-2 px-6 rounded-md bg-navy text-white font-semibold hover:bg-baby-blue transition-colors duration-300">
                    Yakin
                </button>
            </form>
        </div>
    </div>
</div>