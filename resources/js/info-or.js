// File: resources/js/info-or.js

const emptyState = document.getElementById('empty-state');
const tableState = document.getElementById('table-state');
const formState = document.getElementById('form-state');
const pageTitle = document.getElementById('page-title');

const createButton = document.getElementById('create-button');
const emptyCreateButton = document.getElementById('empty-create-button');

// Modal Form
const formModal = document.getElementById('form-modal');
const closeFormModalButton = document.getElementById('close-form-modal');
const formCancelButton = document.getElementById('form-cancel-button');

// Modal Delete
const deleteModal = document.getElementById('delete-modal');
const modalCancelButton = document.getElementById('modal-cancel-button');
const closeForm = document.getElementById('closeForm');

// Modal Gambar
const imageModal = document.getElementById('image-modal');
const modalImage = document.getElementById('modal-image');
const modalJudulInfo = document.getElementById('modal-judul-info');
const closeImageModalButton = document.getElementById('close-image-modal');

// Fungsi untuk mengelola tampilan utama (empty state vs. tabel)
function manageView() {
    // Cek apakah ada baris data di tabel, selain baris "empty"
    const hasData = document.querySelectorAll('#table-state table tbody tr:not(:has(td[colspan]))').length > 0;
    
    if (!hasData) {
        emptyState.classList.remove('hidden');
        tableState.classList.add('hidden');
        if (createButton) {
            createButton.classList.add('hidden');
        }
    } else {
        emptyState.classList.add('hidden');
        tableState.classList.remove('hidden');
        if (createButton) {
            createButton.classList.remove('hidden');
        }
    }
}

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    manageView();

    // Event listener untuk tombol "Buat Info OR"
    if (createButton) {
        createButton.addEventListener('click', () => {
            if (formModal) {
                formModal.classList.remove('hidden');
            }
        });
    }

    // Event listener untuk tombol "Buat Info OR" di empty state
    if (emptyCreateButton) {
        emptyCreateButton.addEventListener('click', () => {
            if (formModal) {
                formModal.classList.remove('hidden');
            }
        });
    }

    // Event listener untuk tombol batal di dalam form modal
    if (formCancelButton) {
        formCancelButton.addEventListener('click', () => {
            if (formModal) {
                formModal.classList.add('hidden');
            }
        });
    }

    // Event listener untuk tombol tutup (X) di form modal
    if (closeFormModalButton) {
        closeFormModalButton.addEventListener('click', () => {
            if (formModal) {
                formModal.classList.add('hidden');
            }
        });
    }

    // Event listener untuk tombol "Tidak" di modal delete
    if (modalCancelButton) {
        modalCancelButton.addEventListener('click', () => {
            if (deleteModal) {
                deleteModal.classList.add('hidden');
            }
        });
    }
    
    // Event listener untuk tombol "Tutup" di tabel
    document.querySelectorAll('.close-button').forEach(button => {
        button.addEventListener('click', (e) => {
            const id = e.target.dataset.id;
            if (closeForm) {
                // Pastikan URL route sudah benar.
                // Jika route Laravel-nya 'info-or.tutup', maka action harusnya seperti ini.
                closeForm.action = `/kelola-info-or/${id}/tutup`; 
                if (deleteModal) {
                    deleteModal.classList.remove('hidden');
                }
            }
        });
    });

    // Event listener untuk tombol "Lihat Gambar"
    document.querySelectorAll('.view-image-button').forEach(button => {
        button.addEventListener('click', (e) => {
            const imageUrl = e.target.dataset.gambarUrl;
            const judul = e.target.dataset.judulInfo;
            
            if (modalImage && modalJudulInfo && imageModal) {
                modalImage.src = imageUrl;
                modalJudulInfo.textContent = judul;
                imageModal.classList.remove('hidden');
            }
        });
    });

    // Event listener untuk tombol tutup (X) di modal gambar
    if (closeImageModalButton) {
        closeImageModalButton.addEventListener('click', () => {
            if (imageModal) {
                imageModal.classList.add('hidden');
                modalImage.src = '';
                modalJudulInfo.textContent = '';
            }
        });
    }
});