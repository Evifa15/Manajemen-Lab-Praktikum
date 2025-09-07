document.addEventListener('DOMContentLoaded', () => {
    
    // ======================================
    // FUNGSI UMUM & NAVIGASI
    // ======================================
    // Fungsi untuk menandai link sidebar aktif
    function setActiveLink(navPrefix, navLinksConfig) {
        const currentPath = window.location.pathname;
        for (const key in navLinksConfig) {
            const element = document.getElementById(navLinksConfig[key]);
            if (element && currentPath.includes(`/${navPrefix}/${key}`)) {
                element.classList.add('active-link');
            }
        }
    }

    // Aktifkan navigasi sidebar untuk setiap peran
    const adminNavs = { 'dashboard': 'nav-dashboard', 'pengguna': 'nav-pengguna', 'barang': 'nav-barang', 'kelas': 'nav-kelas', 'laporan': 'nav-laporan', 'profile': 'nav-profile' };
    const guruNavs = { 'dashboard': 'nav-dashboard-guru', 'verifikasi': 'nav-verifikasi-guru', 'siswa': 'nav-siswa-guru', 'riwayat': 'nav-riwayat-guru', 'profile': 'nav-profile-guru' };
    const siswaNavs = { 'dashboard': 'nav-dashboard-siswa', 'katalog': 'nav-katalog-siswa', 'pengembalian': 'nav-pengembalian-siswa', 'riwayat': 'nav-riwayat-siswa' };
    setActiveLink('admin', adminNavs);
    setActiveLink('guru', guruNavs);
    setActiveLink('siswa', siswaNavs);
    
    // Modal Hapus Universal
    const universalDeleteModal = document.getElementById('deleteModal');
    if (universalDeleteModal) {
        const cancelBtn = universalDeleteModal.querySelector('#cancelDeleteBtn, #cancelDelete');
        const closeBtn = universalDeleteModal.querySelector('.close-button');
        const closeModal = () => { universalDeleteModal.classList.remove('active'); };
        if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        window.addEventListener('click', (e) => { if (e.target === universalDeleteModal) closeModal(); });
    }

    // ======================================
    // LOGIKA SPESIFIK UNTUK SETIAP HALAMAN
    // ======================================

    // Logika untuk Halaman Manajemen Pengguna
    function setupUserPage() {
        const userModal = document.getElementById('userModal');
        const userForm = document.getElementById('userForm');
        const confirmDeleteLink = document.getElementById('confirmDelete');

        const addUserBtn = document.getElementById('addUserBtn');
        if (addUserBtn) {
            addUserBtn.addEventListener('click', () => {
                if (userForm) {
                    userForm.reset();
                    userForm.action = `${BASEURL}/admin/tambah-pengguna`;
                    userForm.querySelector('#password').required = true;
                }
                if (userModal) {
                    userModal.querySelector('h3.modal-title').textContent = 'Tambah Pengguna';
                    userModal.classList.add('active');
                }
            });
        }

        document.querySelectorAll('#userTable .edit-btn').forEach(button => {
            button.addEventListener('click', (event) => {
                const userId = button.dataset.id;
                fetch(`${BASEURL}/admin/get-pengguna-detail-by-id/${userId}`)
                    .then(res => res.json())
                    .then(user => {
                        if (userForm) {
                            userForm.reset();
                            userForm.action = `${BASEURL}/admin/ubah-pengguna`;
                            userForm.querySelector('#userId').value = user.id;
                            
                            userForm.querySelector('#username').value = user.username;
                            userForm.querySelector('#id_pengguna').value = user.id_pengguna;
                            userForm.querySelector('#email').value = user.email;
                            
                            userForm.querySelector('#role').value = user.role;
                            userForm.querySelector('#password').required = false;
                            userForm.querySelector('#password').placeholder = 'Kosongkan jika tidak ingin diubah';
                        }
                        if (userModal) {
                            userModal.querySelector('h3.modal-title').textContent = 'Ubah Pengguna';
                            userModal.classList.add('active');
                        }
                    });
            });
        });

        document.querySelectorAll('#userTable .delete-btn').forEach(button => {
            button.addEventListener('click', () => {
                if (confirmDeleteLink) {
                    confirmDeleteLink.href = `${BASEURL}/admin/hapus-pengguna/${button.dataset.id}`;
                }
                document.getElementById('deleteModal').classList.add('active');
            });
        });

        if (userModal) {
            userModal.querySelector('.close-button').addEventListener('click', () => userModal.classList.remove('active'));
            window.addEventListener('click', (e) => { if (e.target === userModal) userModal.classList.remove('active'); });
        }
    }

    // Logika untuk Halaman Manajemen Barang
    function setupItemPage() {
        const addItemBtn = document.getElementById('addItemBtn');
        if (addItemBtn) {
            addItemBtn.addEventListener('click', () => {
                const itemModal = document.getElementById('itemModal');
                const itemForm = document.getElementById('itemForm');
                
                if (itemForm) {
                    itemForm.reset();
                    itemForm.action = `${BASEURL}/admin/tambah-barang`; 
                }
                if (itemModal) {
                    const formTitle = itemModal.querySelector('h3.modal-title');
                    if (formTitle) formTitle.textContent = 'Tambah Barang';
                    itemModal.classList.add('active');
                }
            });
        }
        
        document.querySelectorAll('#itemTable .edit-btn').forEach(button => {
            button.addEventListener('click', () => {
                const itemId = button.dataset.id;
                const itemModal = document.getElementById('itemModal');
                const itemForm = document.getElementById('itemForm');

                fetch(`${BASEURL}/admin/get-barang-by-id/${itemId}`)
                    .then(res => res.json())
                    .then(item => {
                        if (itemForm) {
                            itemForm.reset();
                            itemForm.action = `${BASEURL}/admin/ubah-barang`;
                            itemForm.querySelector('#itemId').value = item.id;
                            itemForm.querySelector('#gambarLama').value = item.gambar;
                            itemForm.querySelector('#kode_barang').value = item.kode_barang;
                            itemForm.querySelector('#nama_barang').value = item.nama_barang;
                            itemForm.querySelector('#jumlah').value = item.jumlah;
                            itemForm.querySelector('#kondisi').value = item.kondisi;
                            itemForm.querySelector('#tanggal_pembelian').value = item.tanggal_pembelian;
                            itemForm.querySelector('#lokasi_penyimpanan').value = item.lokasi_penyimpanan;
                        }
                        if (itemModal) {
                            const formTitle = itemModal.querySelector('h3.modal-title');
                            if (formTitle) formTitle.textContent = 'Ubah Barang';
                            itemModal.classList.add('active');
                        }
                    });
            });
        });
        
        const confirmDeleteLink = document.getElementById('confirmDeleteLink');
        document.querySelectorAll('#itemTable .delete-btn').forEach(button => {
            button.addEventListener('click', () => {
                if (confirmDeleteLink) {
                    confirmDeleteLink.href = `${BASEURL}/admin/hapus-barang/${button.dataset.id}`;
                }
                document.getElementById('deleteModal').classList.add('active');
            });
        });

        document.querySelectorAll('#itemTable .view-btn').forEach(button => {
            button.addEventListener('click', () => window.location.href = `${BASEURL}/admin/barang/detail/${button.dataset.id}`);
        });

        const itemModal = document.getElementById('itemModal');
        if (itemModal) {
            const closeButton = itemModal.querySelector('.close-button');
            if (closeButton) {
                closeButton.addEventListener('click', () => itemModal.classList.remove('active'));
            }
            window.addEventListener('click', (e) => {
                if (e.target === itemModal) itemModal.classList.remove('active');
            });
        }
    }
    
    // Logika untuk Halaman Manajemen Kelas & Guru
    function setupKelasGuruPage() {
        const tabLinks = document.querySelectorAll('.tab-link');
        const tabContents = document.querySelectorAll('.tab-content');

        tabLinks.forEach(link => {
            link.addEventListener('click', () => {
                const tabId = link.getAttribute('data-tab');
                tabLinks.forEach(l => l.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                link.classList.add('active');
                document.getElementById(tabId).classList.add('active');
            });
        });

        const confirmDelete = document.getElementById('confirmDelete');

        const kelasModal = document.getElementById('kelasModal');
        if (kelasModal) {
            const addKelasBtn = document.getElementById('addKelasBtn');
            const kelasForm = document.getElementById('kelasForm');
            const kelasModalTitle = document.getElementById('kelasModalTitle');

            if (addKelasBtn) {
                addKelasBtn.addEventListener('click', () => {
                    if (kelasForm) {
                        kelasForm.reset();
                        kelasForm.action = `${BASEURL}/admin/tambah-kelas`;
                    }
                    if (kelasModal) {
                        kelasModalTitle.textContent = 'Tambah Kelas';
                        kelasModal.classList.add('active');
                    }
                });
            }
        
            document.querySelectorAll('.edit-kelas-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.id;
                    const kelasForm = document.getElementById('kelasForm');
                    const kelasModalTitle = document.getElementById('kelasModalTitle');

                    fetch(`${BASEURL}/admin/get-kelas-by-id/${id}`)
                        .then(res => res.json())
                        .then(data => {
                            if (kelasForm) {
                                kelasForm.reset();
                                kelasForm.action = `${BASEURL}/admin/ubah-kelas`;
                                document.getElementById('kelasId').value = data.id;
                                document.getElementById('nama_kelas').value = data.nama_kelas;
                                document.getElementById('wali_kelas_id').value = data.wali_kelas_id;
                            }
                            if (kelasModal) {
                                kelasModalTitle.textContent = 'Ubah Kelas';
                                kelasModal.classList.add('active');
                            }
                        });
                });
            });

            document.querySelectorAll('.delete-kelas-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.id;
                    if (confirmDelete) {
                        confirmDelete.href = `${BASEURL}/admin/hapus-kelas/${id}`;
                    }
                    document.getElementById('deleteModal').classList.add('active');
                });
            });
            
            if (kelasModal.querySelector('.close-button')) {
                kelasModal.querySelector('.close-button').addEventListener('click', () => kelasModal.classList.remove('active'));
            }
             window.addEventListener('click', (e) => {
                if (e.target === kelasModal) kelasModal.classList.remove('active');
            });
        }

        const guruModal = document.getElementById('guruModal');
        if (guruModal) {
            const addGuruBtn = document.getElementById('addGuruBtn');
            const guruForm = document.getElementById('guruForm');
            const guruModalTitle = document.getElementById('guruModalTitle');

            if (addGuruBtn) {
                addGuruBtn.addEventListener('click', () => {
                    if (guruForm) {
                        guruForm.reset();
                        guruForm.action = `${BASEURL}/admin/tambah-guru`;
                    }
                    if (guruModal) {
                        guruModalTitle.textContent = 'Tambah Guru';
                        guruModal.classList.add('active');
                    }
                });
            }
        
            document.querySelectorAll('.edit-guru-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.id;
                    const guruForm = document.getElementById('guruForm');
                    const guruModalTitle = document.getElementById('guruModalTitle');

                    fetch(`${BASEURL}/admin/get-guru-by-id/${id}`)
                        .then(res => res.json())
                        .then(data => {
                            if (guruForm) {
                                guruForm.reset();
                                guruForm.action = `${BASEURL}/admin/ubah-guru`;
                                document.getElementById('guruId').value = data.id;
                                document.getElementById('nama').value = data.nama;
                                document.getElementById('nip').value = data.nip;
                                document.getElementById('jenis_kelamin').value = data.jenis_kelamin;
                                document.getElementById('ttl').value = data.ttl;
                                document.getElementById('agama').value = data.agama;
                                document.getElementById('alamat').value = data.alamat;
                                document.getElementById('no_hp').value = data.no_hp;
                                document.getElementById('email').value = data.email;
                            }
                            if (guruModal) {
                                guruModalTitle.textContent = 'Ubah Guru';
                                guruModal.classList.add('active');
                            }
                        });
                });
            });

            document.querySelectorAll('.delete-guru-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.id;
                    if (confirmDelete) {
                        confirmDelete.href = `${BASEURL}/admin/hapus-guru/${id}`;
                    }
                    document.getElementById('deleteModal').classList.add('active');
                });
            });

            if (guruModal.querySelector('.close-button')) {
                guruModal.querySelector('.close-button').addEventListener('click', () => guruModal.classList.remove('active'));
            }
            window.addEventListener('click', (e) => {
                if (e.target === guruModal) guruModal.classList.remove('active');
            });
        }
    }

    // Logika untuk Halaman Detail Kelas
    function setupDetailKelasPage() {
        const siswaModal = document.getElementById('siswaModal');
        const siswaForm = document.getElementById('siswaForm');
        
        const addSiswaBtn = document.getElementById('addSiswaBtn');
        if (addSiswaBtn) {
            addSiswaBtn.addEventListener('click', () => {
                if (siswaForm) {
                    siswaForm.reset();
                    siswaForm.action = `${BASEURL}/admin/tambah-siswa`;
                }
                if (siswaModal) {
                    siswaModal.querySelector('h3.modal-title').textContent = 'Tambah Siswa';
                    siswaModal.classList.add('active');
                }
            });
        }
    
        document.querySelectorAll('.edit-siswa-btn').forEach(button => {
            button.addEventListener('click', () => {
                const siswaId = button.dataset.id;
                const siswaForm = document.getElementById('siswaForm');
                const siswaModal = document.getElementById('siswaModal');

                fetch(`${BASEURL}/admin/get-siswa-by-id/${siswaId}`)
                    .then(res => res.json())
                    .then(siswa => {
                        if (siswaForm) {
                            siswaForm.reset();
                            siswaForm.action = `${BASEURL}/admin/ubah-siswa`;
                            siswaForm.querySelector('#siswaId').value = siswa.id;
                            siswaForm.querySelector('#id_siswa').value = siswa.id_siswa;
                            siswaForm.querySelector('#nama').value = siswa.nama;
                            siswaForm.querySelector('#jenis_kelamin').value = siswa.jenis_kelamin;
                            siswaForm.querySelector('#status').value = siswa.status;
                            siswaForm.querySelector('#ttl').value = siswa.ttl;
                            siswaForm.querySelector('#agama').value = siswa.agama;
                            siswaForm.querySelector('#alamat').value = siswa.alamat;
                            siswaForm.querySelector('#no_hp').value = siswa.no_hp;
                            siswaForm.querySelector('#email').value = siswa.email;
                            siswaForm.querySelector('#kelas_id').value = siswa.kelas_id;
                        }
                        if (siswaModal) {
                            siswaModal.querySelector('h3.modal-title').textContent = 'Ubah Siswa';
                            siswaModal.classList.add('active');
                        }
                    });
            });
        });
        
        // ✅ PERBAIKAN: Logika untuk tombol hapus siswa
        const confirmDelete = document.getElementById('confirmDelete');
        document.querySelectorAll('.delete-siswa-btn').forEach(button => {
            button.addEventListener('click', () => {
                const siswaId = button.dataset.id;
                const kelasId = document.getElementById('kelas_id').value; // Mengambil kelas_id dari input tersembunyi
                if (confirmDelete) {
                    confirmDelete.href = `${BASEURL}/admin/hapus-siswa/${siswaId}/${kelasId}`;
                }
                document.getElementById('deleteModal').classList.add('active');
            });
        });

        if(siswaModal){
            if (siswaModal.querySelector('.close-button')) {
                 siswaModal.querySelector('.close-button').addEventListener('click', () => siswaModal.classList.remove('active'));
            }
            window.addEventListener('click', (e) => {
                if (e.target === siswaModal) siswaModal.classList.remove('active');
            });
        }
    }

    // Logika untuk Halaman Laporan & Riwayat
    function setupLaporanPage() {
        const buktiModal = document.getElementById('buktiModal');
        if (buktiModal) {
            document.querySelectorAll('.view-bukti-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const imageUrl = button.dataset.imgUrl;
                    document.getElementById('buktiModalImg').src = imageUrl;
                    buktiModal.classList.add('active');
                });
            });

            const closeButton = buktiModal.querySelector('.close-button');
            if (closeButton) {
                closeButton.addEventListener('click', () => buktiModal.classList.remove('active'));
            }
            window.addEventListener('click', (e) => {
                if (e.target === buktiModal) buktiModal.classList.remove('active');
            });
        }
    }


    // ======================================
    // PANGGIL FUNGSI BERDASARKAN HALAMAN
    // ======================================
    if (document.getElementById('userTable')) {
        setupUserPage();
    }
    if (document.getElementById('itemTable')) {
        setupItemPage();
    }
    if (document.querySelector('.tab-links-wrapper')) {
        setupKelasGuruPage();
    }
    if (document.querySelector('.manajemen-siswa-container')) {
        setupDetailKelasPage();
    }
    const laporanTable = document.querySelector('#laporan-table');
    if (laporanTable) {
        setupLaporanPage();
    }
});