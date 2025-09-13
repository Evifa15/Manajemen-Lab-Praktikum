// ManajemenLabPraktikum/public/js/admin-script.js

// Fungsi showDeleteModal dipindahkan ke sini, di luar DOMContentLoaded
function showDeleteModal(element) {
    const siswaId = element.dataset.id;
    const deleteModal = document.getElementById('deleteModal');
    
    if (deleteModal) {
        const confirmDeleteLink = deleteModal.querySelector('#confirmDeleteLink');
        if (confirmDeleteLink) {
            confirmDeleteLink.href = `${BASEURL}/admin/hapusSiswaDariKelas/${siswaId}`;
            deleteModal.classList.add('active');
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {

    console.log('admin-script.js dimuat.');

    /**
     * =================================================================
     * FUNGSI-FUNGSI BANTUAN UMUM (GLOBAL HELPER FUNCTIONS)
     * =================================================================
     */
    function setActiveLink(navPrefix, navLinksConfig) {
        const currentPath = window.location.pathname;
        for (const key in navLinksConfig) {
            const element = document.getElementById(navLinksConfig[key]);
            if (element && currentPath.includes(`/${navPrefix}/${key}`)) {
                element.classList.add('active-link');
            }
        }
    }

    function setupModal(modalId, openBtnId, formConfig = null) {
        const modal = document.getElementById(modalId);
        const openBtn = document.getElementById(openBtnId);
        if (!modal || !openBtn) return;
        const closeBtn = modal.querySelector('.close-button');
        openBtn.addEventListener('click', () => {
            console.log(`DEBUG: Tombol Buka Modal ${modalId} diklik.`);
            if (formConfig && formConfig.formId) {
                const form = document.getElementById(formConfig.formId);
                const modalTitle = modal.querySelector('h3');
                if (form) {
                    form.reset();
                    form.action = `${BASEURL}${formConfig.actionUrl}`;
                    if (modalTitle && formConfig.title) {
                        modalTitle.textContent = formConfig.title;
                    }
                    if(form.querySelector('#fotoLama')) {
                        form.querySelector('#fotoLama').value = 'default.png';
                    }
                }
            }
            modal.classList.add('active');
        });
        const closeModal = () => modal.classList.remove('active');
        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        window.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });
    }

    function setupBulkDelete(formId, selectAllId, rowCheckboxClass, bulkDeleteBtnId) {
        const form = document.getElementById(formId);
        if (!form) return;
        const selectAllCheckbox = document.getElementById(selectAllId);
        const bulkDeleteBtn = document.getElementById(bulkDeleteBtnId);
        if (!selectAllCheckbox || !bulkDeleteBtn) return;

        function toggleBulkDeleteBtn() {
            const anyChecked = form.querySelector(`.${rowCheckboxClass}:checked`);
            bulkDeleteBtn.style.display = anyChecked ? 'inline-block' : 'none';
        }

        form.addEventListener('change', function(event) {
            const target = event.target;
            if (target.id === selectAllId) {
                const rowCheckboxes = form.querySelectorAll(`.${rowCheckboxClass}`);
                rowCheckboxes.forEach(checkbox => {
                    checkbox.checked = target.checked;
                });
            }
            if (target.classList.contains(rowCheckboxClass)) {
                const totalCheckboxes = form.querySelectorAll(`.${rowCheckboxClass}`).length;
                const totalChecked = form.querySelectorAll(`.${rowCheckboxClass}:checked`).length;
                selectAllCheckbox.checked = (totalCheckboxes > 0 && totalCheckboxes === totalChecked);
            }
            toggleBulkDeleteBtn();
        });
        
        bulkDeleteBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const deleteModal = document.getElementById('deleteModal');
            // PERBAIKAN DI SINI: Mengubah pencarian dari '#confirmDelete' menjadi '#confirmDeleteLink'
            const confirmDeleteBtn = deleteModal.querySelector('#confirmDeleteLink');
            deleteModal.classList.add('active');
            const handleConfirm = function() {
                form.submit();
            };
            confirmDeleteBtn.addEventListener('click', handleConfirm, { once: true });
        });
    }

    /**
     * =================================================================
     * FUNGSI PENGATURAN UNTUK TIAP HALAMAN ADMIN
     * =================================================================
     */
    function setupManajemenPenggunaPage() {
        console.log('DEBUG: setupManajemenPenggunaPage() dijalankan.');
        
        setupModal('staffModal', 'addStaffBtn', { formId: 'staffForm', actionUrl: '/admin/tambah-staff', title: 'Tambah Staff' });
        setupModal('importStaffModal', 'importStaffBtn');
        setupBulkDelete('bulkDeleteStaffForm', 'selectAllStaff', 'row-checkbox-staff', 'bulkDeleteStaffBtn');

        setupModal('guruModal', 'addGuruBtn', { formId: 'guruForm', actionUrl: '/admin/tambah-guru', title: 'Tambah Guru' });
        setupModal('importGuruModal', 'importGuruBtn');
        setupBulkDelete('bulkDeleteGuruForm', 'selectAllGuru', 'row-checkbox-guru', 'bulkDeleteGuruBtn');

        setupModal('siswaModal', 'addSiswaBtn', { formId: 'siswaForm', actionUrl: '/admin/tambah-siswa', title: 'Tambah Siswa' });
        setupModal('importSiswaModal', 'importSiswaBtn');
        setupBulkDelete('bulkDeleteSiswaForm', 'selectAllSiswa', 'row-checkbox-siswa', 'bulkDeleteSiswaBtn');

        const staffTableBody = document.getElementById('staffTableBody');
        const guruTableBody = document.getElementById('guruTableBody');
        const siswaTableBody = document.getElementById('siswaTableBody');
        const akunTableBody = document.getElementById('akunTableBody');
        const deleteModal = document.getElementById('deleteModal');
        const staffModal = document.getElementById('staffModal');
        const guruModal = document.getElementById('guruModal');
        const siswaModal = document.getElementById('siswaModal');
        const ubahPasswordModal = document.getElementById('ubahPasswordModal');

        if (staffTableBody) {
            staffTableBody.addEventListener('click', function(event) {
                const target = event.target;
                const deleteButton = target.closest('.delete-staff-btn');
                const editButton = target.closest('.edit-staff-btn');
                if (deleteButton) {
                    const confirmDeleteLink = deleteModal.querySelector('#confirmDelete');
                    confirmDeleteLink.href = `${BASEURL}/admin/hapus-staff/${deleteButton.dataset.id}`;
                    deleteModal.classList.add('active');
                }
                if (editButton) {
                    const staffForm = document.getElementById('staffForm');
                    const staffModalTitle = staffModal.querySelector('h3');
                    staffModalTitle.textContent = 'Ubah Data Staff';
                    staffForm.action = `${BASEURL}/admin/ubah-staff`;
                    fetch(`${BASEURL}/admin/get-staff-by-id/${editButton.dataset.id}`)
                        .then(response => response.json())
                        .then(data => {
                            staffForm.querySelector('#staffId').value = data.id;
                            staffForm.querySelector('#nama').value = data.nama;
                            staffForm.querySelector('#id_staff').value = data.id_staff;
                            staffForm.querySelector('#jenis_kelamin_staff').value = data.jenis_kelamin;
                            staffForm.querySelector('#ttl_staff').value = data.ttl;
                            staffForm.querySelector('#agama_staff').value = data.agama;
                            staffForm.querySelector('#no_hp_staff').value = data.no_hp;
                            staffForm.querySelector('#alamat_staff').value = data.alamat;
                            staffForm.querySelector('#email_staff').value = data.email;
                            staffModal.classList.add('active');
                        });
                }
            });
        }
        
        if (guruTableBody) {
            guruTableBody.addEventListener('click', function(event) {
                const target = event.target;
                const deleteButton = target.closest('.delete-guru-btn');
                const editButton = target.closest('.edit-guru-btn');
                if (deleteButton) {
                    const confirmDeleteLink = deleteModal.querySelector('#confirmDelete');
                    confirmDeleteLink.href = `${BASEURL}/admin/hapus-guru/${deleteButton.dataset.id}`;
                    deleteModal.classList.add('active');
                }
                if (editButton) {
                    const guruForm = document.getElementById('guruForm');
                    const guruModalTitle = guruModal.querySelector('h3');
                    guruModalTitle.textContent = 'Ubah Data Guru';
                    guruForm.action = `${BASEURL}/admin/ubah-guru`;
                    fetch(`${BASEURL}/admin/get-guru-by-id/${editButton.dataset.id}`)
                        .then(response => response.json())
                        .then(data => {
                            guruForm.querySelector('#guruId').value = data.id;
                            guruForm.querySelector('#nama_guru').value = data.nama;
                            guruForm.querySelector('#nip_guru').value = data.nip;
                            guruForm.querySelector('#jenis_kelamin_guru').value = data.jenis_kelamin;
                            guruForm.querySelector('#ttl_guru').value = data.ttl;
                            guruForm.querySelector('#agama_guru').value = data.agama;
                            guruForm.querySelector('#no_hp_guru').value = data.no_hp;
                            guruForm.querySelector('#alamat_guru').value = data.alamat;
                            guruForm.querySelector('#email_guru').value = data.email;
                            guruModal.classList.add('active');
                        });
                }
            });
        }

<<<<<<< HEAD
        if (siswaTableBody) {
=======
         if (siswaTableBody) {
>>>>>>> d91e78e571d94349aa3d4bebddb2d5d66b3b0cbd
            siswaTableBody.addEventListener('click', function(event) {
                const target = event.target;
                const deleteButton = target.closest('.delete-siswa-btn');
                const editButton = target.closest('.edit-siswa-btn');
                
                if (deleteButton) {
                    const confirmDeleteLink = deleteModal.querySelector('#confirmDelete');
                    confirmDeleteLink.href = `${BASEURL}/admin/hapus-siswa/${deleteButton.dataset.id}`;
                    deleteModal.classList.add('active');
                }

                if (editButton) {
                    const siswaForm = document.getElementById('siswaForm');
                    const siswaModalTitle = siswaModal.querySelector('h3');
                    siswaModalTitle.textContent = 'Ubah Data Siswa';
                    siswaForm.action = `${BASEURL}/admin/ubah-siswa`;
                    
                    fetch(`${BASEURL}/admin/get-siswa-by-id/${editButton.dataset.id}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            siswaForm.querySelector('#siswaId').value = data.id;
                            siswaForm.querySelector('#nama_siswa').value = data.nama;
                            siswaForm.querySelector('#id_siswa').value = data.id_siswa;
                            siswaForm.querySelector('#jenis_kelamin_siswa').value = data.jenis_kelamin;
                            siswaForm.querySelector('#ttl_siswa').value = data.ttl;
                            siswaForm.querySelector('#agama_siswa').value = data.agama;
                            siswaForm.querySelector('#no_hp_siswa').value = data.no_hp;
                            siswaForm.querySelector('#alamat_siswa').value = data.alamat;
                            siswaForm.querySelector('#email_siswa').value = data.email;
                            siswaForm.querySelector('#fotoLama').value = data.foto;
                            siswaModal.classList.add('active');
                        })
                        .catch(error => {
                            console.error('Error fetching student data:', error);
                        });
                }
            });
        }
<<<<<<< HEAD
        
=======
>>>>>>> d91e78e571d94349aa3d4bebddb2d5d66b3b0cbd
        if (akunTableBody) {
             akunTableBody.addEventListener('click', function(event) {
                const target = event.target;
                const editButton = target.closest('.edit-akun-btn');
                if (editButton) {
                    const ubahPasswordForm = document.getElementById('ubahPasswordForm');
                    const usernameAkun = document.getElementById('username-akun');

                    ubahPasswordForm.querySelector('#akunId').value = editButton.dataset.id;
                    ubahPasswordForm.querySelector('#password-baru').value = '';
                    ubahPasswordForm.querySelector('#konfirmasi-password').value = '';
                    usernameAkun.textContent = editButton.dataset.username;
                    ubahPasswordModal.classList.add('active');
                }
            });
        }
    }

<<<<<<< HEAD
    function setupManajemenBarangPage() {
        console.log('DEBUG: setupManajemenBarangPage() dijalankan.');

        // 1. Setup modal untuk Tambah Barang
        setupModal('itemModal', 'addItemBtn', { 
            formId: 'itemForm', 
            actionUrl: '/admin/tambah-barang', 
            title: 'Tambah Barang Baru' 
        });
        
        // 2. Setup modal untuk Import Barang (BARU)
        setupModal('importItemModal', 'importItemBtn');

        const itemTable = document.querySelector('#itemTable tbody');
        const itemModal = document.getElementById('itemModal');
        const itemForm = document.getElementById('itemForm');
        const itemModalTitle = itemModal.querySelector('.modal-title');
        const deleteModal = document.getElementById('deleteModal');

        if (!itemTable) return;

        // 3. Setup listener untuk tombol-tombol aksi di dalam tabel
        itemTable.addEventListener('click', (event) => {
            const target = event.target.closest('button, a');
            if (!target) return;

            // Logika untuk tombol View
            if (target.matches('.view-btn')) {
                const itemId = target.dataset.id;
                window.location.href = `${BASEURL}/admin/barang/detail/${itemId}`;
            }

            // Logika untuk tombol Edit
            if (target.matches('.edit-btn')) {
                const itemId = target.dataset.id;
                
                itemModalTitle.textContent = 'Ubah Data Barang';
                itemForm.action = `${BASEURL}/admin/ubah-barang`;

                fetch(`${BASEURL}/admin/get-barang-by-id/${itemId}`)
                    .then(response => response.json())
                    .then(data => {
                        itemForm.querySelector('#itemId').value = data.id;
                        itemForm.querySelector('#kode_barang').value = data.kode_barang;
                        itemForm.querySelector('#nama_barang').value = data.nama_barang;
                        itemForm.querySelector('#jumlah').value = data.jumlah;
                        itemForm.querySelector('#kondisi').value = data.kondisi;
                        itemForm.querySelector('#lokasi_penyimpanan').value = data.lokasi_penyimpanan;
                        itemForm.querySelector('#tanggal_pembelian').value = data.tanggal_pembelian;
                        // Simpan nama gambar lama untuk diproses di PHP
                        itemForm.querySelector('#gambarLama').value = data.gambar;
                        itemModal.classList.add('active');
                    })
                    .catch(error => console.error('Error fetching item data:', error));
            }

            // Logika untuk tombol Delete
            if (target.matches('.delete-btn')) {
                const itemId = target.dataset.id;
                const confirmDeleteLink = deleteModal.querySelector('#confirmDeleteLink');
                confirmDeleteLink.href = `${BASEURL}/admin/hapus-barang/${itemId}`;
                deleteModal.classList.add('active');
            }
        });
    }
=======
    // ManajemenLabPraktikum/public/js/admin-script.js

/**
 * =================================================================
 * FUNGSI PENGATURAN UNTUK TIAP HALAMAN ADMIN
 * =================================================================
 */
 function setupManajemenPenggunaPage() {
    console.log('DEBUG: setupManajemenPenggunaPage() dijalankan.');
    
    setupModal('staffModal', 'addStaffBtn', { formId: 'staffForm', actionUrl: '/admin/tambah-staff', title: 'Tambah Staff' });
    setupModal('importStaffModal', 'importStaffBtn');
    setupBulkDelete('bulkDeleteStaffForm', 'selectAllStaff', 'row-checkbox-staff', 'bulkDeleteStaffBtn');

    setupModal('guruModal', 'addGuruBtn', { formId: 'guruForm', actionUrl: '/admin/tambah-guru', title: 'Tambah Guru' });
    setupModal('importGuruModal', 'importGuruBtn');
    setupBulkDelete('bulkDeleteGuruForm', 'selectAllGuru', 'row-checkbox-guru', 'bulkDeleteGuruBtn');

    setupModal('siswaModal', 'addSiswaBtn', { formId: 'siswaForm', actionUrl: '/admin/tambah-siswa', title: 'Tambah Siswa' });
    setupModal('importSiswaModal', 'importSiswaBtn');
    setupBulkDelete('bulkDeleteSiswaForm', 'selectAllSiswa', 'row-checkbox-siswa', 'bulkDeleteSiswaBtn');

    const staffTableBody = document.getElementById('staffTableBody');
    const guruTableBody = document.getElementById('guruTableBody');
    const siswaTableBody = document.getElementById('siswaTableBody');
    const akunTableBody = document.getElementById('akunTableBody');
    const deleteModal = document.getElementById('deleteModal');
    const staffModal = document.getElementById('staffModal');
    const guruModal = document.getElementById('guruModal');
    const siswaModal = document.getElementById('siswaModal');
    const ubahPasswordModal = document.getElementById('ubahPasswordModal');

    if (staffTableBody) {
        staffTableBody.addEventListener('click', function(event) {
            const target = event.target;
            const deleteButton = target.closest('.delete-staff-btn');
            const editButton = target.closest('.edit-staff-btn');
            if (deleteButton) {
                const confirmDeleteLink = deleteModal.querySelector('#confirmDelete');
                confirmDeleteLink.href = `${BASEURL}/admin/hapus-staff/${deleteButton.dataset.id}`;
                deleteModal.classList.add('active');
            }
            if (editButton) {
                const staffForm = document.getElementById('staffForm');
                const staffModalTitle = staffModal.querySelector('h3');
                staffModalTitle.textContent = 'Ubah Data Staff';
                staffForm.action = `${BASEURL}/admin/ubah-staff`;
                fetch(`${BASEURL}/admin/get-staff-by-id/${editButton.dataset.id}`)
                    .then(response => response.json())
                    .then(data => {
                        staffForm.querySelector('#staffId').value = data.id;
                        staffForm.querySelector('#nama').value = data.nama;
                        staffForm.querySelector('#id_staff').value = data.id_staff;
                        staffForm.querySelector('#jenis_kelamin_staff').value = data.jenis_kelamin;
                        staffForm.querySelector('#ttl_staff').value = data.ttl;
                        staffForm.querySelector('#agama_staff').value = data.agama;
                        staffForm.querySelector('#no_hp_staff').value = data.no_hp;
                        staffForm.querySelector('#alamat_staff').value = data.alamat;
                        staffForm.querySelector('#email_staff').value = data.email;
                        staffModal.classList.add('active');
                    });
                }
            });
        }
        
        if (guruTableBody) {
            guruTableBody.addEventListener('click', function(event) {
                const target = event.target;
                const deleteButton = target.closest('.delete-guru-btn');
                const editButton = target.closest('.edit-guru-btn');
                if (deleteButton) {
                    const confirmDeleteLink = deleteModal.querySelector('#confirmDelete');
                    confirmDeleteLink.href = `${BASEURL}/admin/hapus-guru/${deleteButton.dataset.id}`;
                    deleteModal.classList.add('active');
                }
                if (editButton) {
                    const guruForm = document.getElementById('guruForm');
                    const guruModalTitle = guruModal.querySelector('h3');
                    guruModalTitle.textContent = 'Ubah Data Guru';
                    guruForm.action = `${BASEURL}/admin/ubah-guru`;
                    fetch(`${BASEURL}/admin/get-guru-by-id/${editButton.dataset.id}`)
                        .then(response => response.json())
                        .then(data => {
                            guruForm.querySelector('#guruId').value = data.id;
                            guruForm.querySelector('#nama_guru').value = data.nama;
                            guruForm.querySelector('#nip_guru').value = data.nip;
                            guruForm.querySelector('#jenis_kelamin_guru').value = data.jenis_kelamin;
                            guruForm.querySelector('#ttl_guru').value = data.ttl;
                            guruForm.querySelector('#agama_guru').value = data.agama;
                            guruForm.querySelector('#no_hp_guru').value = data.no_hp;
                            guruForm.querySelector('#alamat_guru').value = data.alamat;
                            guruForm.querySelector('#email_guru').value = data.email;
                            guruModal.classList.add('active');
                        });
                }
            });
        }

         if (siswaTableBody) {
            siswaTableBody.addEventListener('click', function(event) {
                const target = event.target;
                const deleteButton = target.closest('.delete-siswa-btn');
                const editButton = target.closest('.edit-siswa-btn');
                
                if (deleteButton) {
                    const confirmDeleteLink = deleteModal.querySelector('#confirmDelete');
                    confirmDeleteLink.href = `${BASEURL}/admin/hapus-siswa/${deleteButton.dataset.id}`;
                    deleteModal.classList.add('active');
                }

                if (editButton) {
                    const siswaForm = document.getElementById('siswaForm');
                    const siswaModalTitle = siswaModal.querySelector('h3');
                    siswaModalTitle.textContent = 'Ubah Data Siswa';
                    siswaForm.action = `${BASEURL}/admin/ubah-siswa`;
                    
                    fetch(`${BASEURL}/admin/get-siswa-by-id/${editButton.dataset.id}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            siswaForm.querySelector('#siswaId').value = data.id;
                            siswaForm.querySelector('#nama_siswa').value = data.nama;
                            siswaForm.querySelector('#id_siswa').value = data.id_siswa;
                            siswaForm.querySelector('#jenis_kelamin_siswa').value = data.jenis_kelamin;
                            siswaForm.querySelector('#ttl_siswa').value = data.ttl;
                            siswaForm.querySelector('#agama_siswa').value = data.agama;
                            siswaForm.querySelector('#no_hp_siswa').value = data.no_hp;
                            siswaForm.querySelector('#alamat_siswa').value = data.alamat;
                            siswaForm.querySelector('#email_siswa').value = data.email;
                            siswaForm.querySelector('#fotoLama').value = data.foto;
                            siswaModal.classList.add('active');
                        })
                        .catch(error => {
                            console.error('Error fetching student data:', error);
                        });
                }
            });
        }
        if (akunTableBody) {
             akunTableBody.addEventListener('click', function(event) {
                const target = event.target;
                const editButton = target.closest('.edit-akun-btn');
                if (editButton) {
                    const ubahPasswordForm = document.getElementById('ubahPasswordForm');
                    const usernameAkun = document.getElementById('username-akun');

                    ubahPasswordForm.querySelector('#akunId').value = editButton.dataset.id;
                    ubahPasswordForm.querySelector('#password-baru').value = '';
                    ubahPasswordForm.querySelector('#konfirmasi-password').value = '';
                    usernameAkun.textContent = editButton.dataset.username;
                    ubahPasswordModal.classList.add('active');
                }
            });
        }
    }
>>>>>>> d91e78e571d94349aa3d4bebddb2d5d66b3b0cbd

    function setupDetailKelasPage() {
        console.log('DEBUG: setupDetailKelasPage() dijalankan.');
        
<<<<<<< HEAD
        setupModal('assignSiswaModal', 'addSiswaBtn', { formId: 'assignSiswaForm', actionUrl: '/admin/assignSiswaToKelas', title: 'Tambahkan Siswa ke Kelas' });
        setupModal('importSiswaModal', 'importSiswaBtn', { formId: 'importSiswaForm', actionUrl: '/admin/importSiswaKeKelas', title: 'Import Siswa' });
        setupBulkDelete('bulkDeleteSiswaForm', 'selectAllSiswa', 'row-checkbox-siswa', 'bulkDeleteSiswaBtn');
        
        const siswaTableBody = document.querySelector('tbody');
        const deleteModal = document.getElementById('deleteModal');
        const editStatusModal = document.getElementById('editSiswaStatusModal'); // Ambil modal baru

        if (siswaTableBody) {
            siswaTableBody.addEventListener('click', function(event) {
                const target = event.target.closest('button, a');
                if (!target) return;

                // Logika Hapus Tunggal (dari kelas)
                if (target.matches('.delete-siswa-btn')) {
                    event.preventDefault();
                    const siswaId = target.dataset.id;
                    const confirmDeleteLink = deleteModal.querySelector('#confirmDeleteLink');
                    if (confirmDeleteLink) {
                        confirmDeleteLink.href = `${BASEURL}/admin/hapusSiswaDariKelas/${siswaId}`;
                        deleteModal.classList.add('active');
                    }
                }

                // LOGIKA BARU: Tombol Edit Status
                if (target.matches('.edit-status-btn')) {
                    event.preventDefault();
                    const siswaId = target.dataset.id;
                    const siswaNama = target.dataset.nama;
                    const currentStatus = target.dataset.status;

                    if (editStatusModal) {
                        editStatusModal.querySelector('#editStatusSiswaId').value = siswaId;
                        editStatusModal.querySelector('#namaSiswaStatus').textContent = siswaNama;
                        editStatusModal.querySelector('#status_siswa').value = currentStatus;
                        editStatusModal.classList.add('active');
=======
        // Setup modal untuk tambah siswa
        setupModal('assignSiswaModal', 'addSiswaBtn', { formId: 'assignSiswaForm', actionUrl: '/admin/assignSiswaToKelas', title: 'Tambahkan Siswa ke Kelas' });
        
        // Setup modal untuk import siswa
        setupModal('importSiswaModal', 'importSiswaBtn', { formId: 'importSiswaForm', actionUrl: '/admin/importSiswaKeKelas', title: 'Import Siswa' });
        
        // Setup fungsionalitas hapus massal
        setupBulkDelete('bulkDeleteSiswaForm', 'selectAllSiswa', 'row-checkbox-siswa', 'bulkDeleteSiswaBtn');
        
        const siswaTableBody = document.getElementById('siswaTableBody');
        const deleteModal = document.getElementById('deleteModal');
        
        // Logika untuk tombol HAPUS tunggal
        if (siswaTableBody) {
            siswaTableBody.addEventListener('click', function(event) {
                const target = event.target;
                const deleteButton = target.closest('.delete-siswa-btn');
                
                if (deleteButton) {
                    event.preventDefault();
                    event.stopPropagation();
                    
                    const siswaId = deleteButton.dataset.id;
                    
                    if (deleteModal) {
                        const confirmDeleteLink = deleteModal.querySelector('#confirmDeleteLink');
                        if (confirmDeleteLink) {
                            confirmDeleteLink.href = `${BASEURL}/admin/hapusSiswaDariKelas/${siswaId}`;
                            deleteModal.classList.add('active');
                        }
>>>>>>> d91e78e571d94349aa3d4bebddb2d5d66b3b0cbd
                    }
                }
            });
        }

<<<<<<< HEAD
=======
        // Logika Pencarian Dinamis untuk Modal Tambah Siswa (Sudah diintegrasikan)
>>>>>>> d91e78e571d94349aa3d4bebddb2d5d66b3b0cbd
        const searchUnassignedInput = document.getElementById('searchUnassignedSiswaInput');
        const siswaSelect = document.getElementById('siswa_id_select');

        if (searchUnassignedInput && siswaSelect) {
            let timeout = null;
            
            searchUnassignedInput.addEventListener('input', (e) => {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    const keyword = e.target.value.trim();
                    
                    fetch(`${BASEURL}/admin/searchUnassignedSiswa?keyword=${encodeURIComponent(keyword)}`)
                        .then(response => response.json())
                        .then(data => {
                            siswaSelect.innerHTML = '<option value="">-- Pilih Siswa --</option>';

                            if (data.length > 0) {
                                data.forEach(siswa => {
                                    const option = document.createElement('option');
                                    option.value = siswa.id;
                                    option.textContent = `${siswa.nama} (NIS: ${siswa.id_siswa})`;
                                    siswaSelect.appendChild(option);
                                });
                            } else {
                                const option = document.createElement('option');
                                option.value = '';
                                option.disabled = true;
                                option.textContent = 'Tidak ada siswa yang ditemukan.';
                                siswaSelect.appendChild(option);
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching search results:', error);
                        });
                }, 500);
            });
        }
    }


    /**
     * =================================================================
     * EKSEKUSI UTAMA (MAIN EXECUTION)
     * =================================================================
     */
    
    const currentPath = window.location.pathname;

    if (currentPath.includes('/admin/pengguna')) {
        setupManajemenPenggunaPage();
    } else if (currentPath.includes('/admin/kelas') && !currentPath.includes('/admin/detailKelas')) {
        setupManajemenKelasPage();
    } else if (currentPath.includes('/admin/detailKelas')) {
        setupDetailKelasPage();
<<<<<<< HEAD
    }
    else if (currentPath.includes('/admin/barang')) {
        setupManajemenBarangPage();
=======
>>>>>>> d91e78e571d94349aa3d4bebddb2d5d66b3b0cbd
    }

    const adminNavs = { 'dashboard': 'nav-dashboard', 'pengguna': 'nav-pengguna', 'barang': 'nav-barang', 'kelas': 'nav-kelas', 'laporan': 'nav-laporan', 'profile': 'nav-profile' };
    setActiveLink('admin', adminNavs);

    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        const closeModal = () => deleteModal.classList.remove('active');
        const cancelBtn = deleteModal.querySelector('#cancelDelete');
        const closeBtn = deleteModal.querySelector('.close-button');
        if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        window.addEventListener('click', (event) => {
            if (event.target === deleteModal) closeModal();
        });
    }

    function setupStandardSearch(formId) {
        const searchForm = document.getElementById(formId);
        if (searchForm) {
            searchForm.addEventListener('submit', () => {
            });
        }
    }
    
    setupStandardSearch('searchStaffForm');
    setupStandardSearch('searchGuruForm');
    setupStandardSearch('searchSiswaForm');
    setupStandardSearch('searchAkunForm');
    setupStandardSearch('searchKelasForm');
});