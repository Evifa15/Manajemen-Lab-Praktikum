document.addEventListener('DOMContentLoaded', () => {
    
    // ======================================
    // LOGIKA SPESIFIK UNTUK HALAMAN PENGGUNA
    // ======================================
    const userPage = document.getElementById('userTable');
    if (userPage) {
        const userModal = document.getElementById('userModal');
        const addUserBtn = document.getElementById('addUserBtn');
        const userForm = document.getElementById('userForm');
        const confirmDeleteLink = document.getElementById('confirmDelete');

        addUserBtn.addEventListener('click', () => {
            userForm.reset();
            userModal.querySelector('h3.modal-title').textContent = 'Tambah Pengguna';
            userForm.action = `${BASEURL}/admin/tambah-pengguna`;
            userModal.querySelector('#password').required = true;
            userModal.style.display = 'flex';
        });

        document.querySelectorAll('#userTable .edit-btn').forEach(button => {
            button.addEventListener('click', (event) => {
                const userId = button.dataset.id;
                // Panggil endpoint baru yang lebih lengkap
                fetch(`${BASEURL}/admin/get-pengguna-detail-by-id/${userId}`)
                    .then(res => res.json())
                    .then(user => {
                        userForm.reset();
                        userModal.querySelector('h3.modal-title').textContent = 'Ubah Pengguna';
                        userForm.action = `${BASEURL}/admin/ubah-pengguna`;
                        userForm.querySelector('#userId').value = user.id;
                        userForm.querySelector('#username').value = user.username;
                        
                        // Isi field dari data gabungan
                        userForm.querySelector('#id_pengguna').value = user.id_pengguna;
                        userForm.querySelector('#email').value = user.email;
                        
                        userForm.querySelector('#role').value = user.role;
                        userForm.querySelector('#password').required = false;
                        userForm.querySelector('#password').placeholder = 'Kosongkan jika tidak ingin diubah';
                        userModal.style.display = 'flex';
                    });
            });
        });

        document.querySelectorAll('#userTable .delete-btn').forEach(button => {
            button.addEventListener('click', () => {
                if (confirmDeleteLink) {
                    confirmDeleteLink.href = `${BASEURL}/admin/hapus-pengguna/${button.dataset.id}`;
                    document.getElementById('deleteModal').style.display = 'flex';
                }
            });
        });

        if (userModal) {
            userModal.querySelector('.close-button').addEventListener('click', () => userModal.style.display = 'none');
            window.addEventListener('click', (e) => { if (e.target === userModal) userModal.style.display = 'none'; });
        }
    }
    
    // ======================================
    // LOGIKA SPESIFIK UNTUK HALAMAN BARANG
    // ======================================
    const itemPage = document.getElementById('itemTable');
    if (itemPage) {
        const itemModal = document.getElementById('itemModal');
        const addItemBtn = document.getElementById('addItemBtn');
        const itemForm = document.getElementById('itemForm');
        const confirmDeleteLink = document.getElementById('confirmDeleteLink');

        addItemBtn.addEventListener('click', () => {
            itemForm.reset();
            itemModal.querySelector('h3.modal-title').textContent = 'Tambah Barang';
            itemForm.action = `${BASEURL}/admin/tambah-barang`; 
            itemModal.style.display = 'flex';
        });
        
        document.querySelectorAll('#itemTable .edit-btn').forEach(button => {
            button.addEventListener('click', () => {
                const itemId = button.dataset.id;
                fetch(`${BASEURL}/admin/get-barang-by-id/${itemId}`)
                    .then(res => res.json())
                    .then(item => {
                        itemForm.reset();
                        itemModal.querySelector('h3.modal-title').textContent = 'Ubah Barang';
                        itemForm.action = `${BASEURL}/admin/ubah-barang`;
                        itemForm.querySelector('#itemId').value = item.id;
                        itemForm.querySelector('#kode_barang').value = item.kode_barang;
                        itemForm.querySelector('#nama_barang').value = item.nama_barang;
                        itemForm.querySelector('#jumlah').value = item.jumlah;
                        itemForm.querySelector('#kondisi').value = item.kondisi;
                        itemForm.querySelector('#tanggal_pembelian').value = item.tanggal_pembelian;
                        itemForm.querySelector('#lokasi_penyimpanan').value = item.lokasi_penyimpanan;
                        itemModal.style.display = 'flex';
                    });
            });
        });

        document.querySelectorAll('#itemTable .delete-btn').forEach(button => {
            button.addEventListener('click', () => {
                if (confirmDeleteLink) {
                    confirmDeleteLink.href = `${BASEURL}/admin/hapus-barang/${button.dataset.id}`;
                    document.getElementById('deleteModal').style.display = 'flex';
                }
            });
        });

        document.querySelectorAll('#itemTable .view-btn').forEach(button => {
            button.addEventListener('click', () => window.location.href = `${BASEURL}/admin/barang/detail/${button.dataset.id}`);
        });

        if(itemModal){
            itemModal.querySelector('.close-button').addEventListener('click', () => itemModal.style.display = 'none');
            window.addEventListener('click', (e) => { if (e.target === itemModal) itemModal.style.display = 'none'; });
        }
    }

    // ===================================================
    // LOGIKA UNTUK HALAMAN MANAJEMEN KELAS & GURU
    // ===================================================
    const kelasPage = document.querySelector('.tab-container');
    if (kelasPage) {
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

            addKelasBtn.addEventListener('click', () => {
                kelasForm.reset();
                kelasForm.action = `${BASEURL}/admin/tambah-kelas`;
                kelasModalTitle.textContent = 'Tambah Kelas';
                kelasModal.style.display = 'flex';
            });
        
            document.querySelectorAll('.edit-kelas-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.id;
                    fetch(`${BASEURL}/admin/get-kelas-by-id/${id}`)
                        .then(res => res.json())
                        .then(data => {
                            kelasForm.reset();
                            kelasForm.action = `${BASEURL}/admin/ubah-kelas`;
                            kelasModalTitle.textContent = 'Ubah Kelas';
                            document.getElementById('kelasId').value = data.id;
                            document.getElementById('nama_kelas').value = data.nama_kelas;
                            document.getElementById('wali_kelas_id').value = data.wali_kelas_id;
                            kelasModal.style.display = 'flex';
                        });
                });
            });

            document.querySelectorAll('.delete-kelas-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.id;
                    confirmDelete.href = `${BASEURL}/admin/hapus-kelas/${id}`;
                    document.getElementById('deleteModal').style.display = 'flex';
                });
            });
            
            kelasModal.querySelector('.close-button').addEventListener('click', () => kelasModal.style.display = 'none');
             window.addEventListener('click', (e) => {
                if (e.target === kelasModal) kelasModal.style.display = 'none';
            });
        }

        const guruModal = document.getElementById('guruModal');
        if (guruModal) {
            const addGuruBtn = document.getElementById('addGuruBtn');
            const guruForm = document.getElementById('guruForm');
            const guruModalTitle = document.getElementById('guruModalTitle');

            addGuruBtn.addEventListener('click', () => {
                guruForm.reset();
                guruForm.action = `${BASEURL}/admin/tambah-guru`;
                guruModalTitle.textContent = 'Tambah Guru';
                guruModal.style.display = 'flex';
            });
        
            document.querySelectorAll('.edit-guru-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.id;
                    fetch(`${BASEURL}/admin/get-guru-by-id/${id}`)
                        .then(res => res.json())
                        .then(data => {
                            guruForm.reset();
                            guruForm.action = `${BASEURL}/admin/ubah-guru`;
                            guruModalTitle.textContent = 'Ubah Guru';
                            document.getElementById('guruId').value = data.id;
                            document.getElementById('nama').value = data.nama;
                            document.getElementById('nip').value = data.nip;
                            document.getElementById('jenis_kelamin').value = data.jenis_kelamin;
                            document.getElementById('ttl').value = data.ttl;
                            document.getElementById('agama').value = data.agama;
                            document.getElementById('alamat').value = data.alamat;
                            document.getElementById('no_hp').value = data.no_hp;
                            document.getElementById('email').value = data.email;
                            guruModal.style.display = 'flex';
                        });
                });
            });

            document.querySelectorAll('.delete-guru-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.id;
                    confirmDelete.href = `${BASEURL}/admin/hapus-guru/${id}`;
                    document.getElementById('deleteModal').style.display = 'flex';
                });
            });

            guruModal.querySelector('.close-button').addEventListener('click', () => guruModal.style.display = 'none');
            window.addEventListener('click', (e) => {
                if (e.target === guruModal) guruModal.style.display = 'none';
            });
        }
    }

    // ============================================================
    // LOGIKA UNTUK MANAJEMEN SISWA DI HALAMAN DETAIL KELAS
    // ============================================================
    const detailKelasPage = document.querySelector('.manajemen-siswa-container');
    if (detailKelasPage) {
        // ... (Kode ini spesifik untuk admin, jadi tetap di sini)
    }

    // ======================================
    // LOGIKA UNTUK MODAL BUKTI PENGEMBALIAN
    // ======================================
    const buktiModal = document.getElementById('buktiModal');
    if (buktiModal) {
        // ... (Kode ini spesifik untuk admin, jadi tetap di sini)
    }
});