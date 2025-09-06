document.addEventListener('DOMContentLoaded', () => {
    
    // Fungsi umum untuk menandai link aktif
    function setActiveLink(navPrefix, navLinksConfig) {
        const currentPath = window.location.pathname;
        for (const key in navLinksConfig) {
            const element = document.getElementById(navLinksConfig[key]);
            if (element && currentPath.includes(`/${navPrefix}/${key}`)) {
                element.classList.add('active-link');
            }
        }
    }

    if (document.querySelector('.app-container')) {
        
        const adminNavs = {
            'dashboard': 'nav-dashboard', 'pengguna': 'nav-pengguna',
            'barang': 'nav-barang', 'kelas': 'nav-kelas',
            'laporan': 'nav-laporan', 'profile': 'nav-profile'
        };
        const guruNavs = {
            'dashboard': 'nav-dashboard-guru', 'verifikasi': 'nav-verifikasi-guru',
            'siswa': 'nav-siswa-guru', 'riwayat': 'nav-riwayat-guru',
            'profile': 'nav-profile-guru'
        };
        const siswaNavs = {
            'dashboard': 'nav-dashboard-siswa', 'katalog': 'nav-katalog-siswa',
            'pengembalian': 'nav-pengembalian-siswa', 'riwayat': 'nav-riwayat-siswa'
        };

        setActiveLink('admin', adminNavs);
        setActiveLink('guru', guruNavs);
        setActiveLink('siswa', siswaNavs);

        const universalDeleteModal = document.getElementById('deleteModal');
        if (universalDeleteModal) {
            const cancelBtn = universalDeleteModal.querySelector('#cancelDeleteBtn, #cancelDelete');
            const closeBtn = universalDeleteModal.querySelector('.close-button');
            const closeModal = () => { universalDeleteModal.style.display = 'none'; };
            if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
            if (closeBtn) closeBtn.addEventListener('click', closeModal);
            window.addEventListener('click', (e) => { if (e.target === universalDeleteModal) closeModal(); });
        }

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
                userForm.action = `${BASEURL}/admin/tambahPengguna`;
                userModal.querySelector('#password').required = true;
                userModal.style.display = 'flex';
            });

            // ✅ PERBAIKAN: Listener sekarang lebih spesifik untuk tabel pengguna
            document.querySelectorAll('#userTable .edit-btn').forEach(button => {
                button.addEventListener('click', (event) => {
                    const userId = button.dataset.id;
                    fetch(`${BASEURL}/admin/getPenggunaById/${userId}`)
                        .then(res => res.json())
                        .then(user => {
                            userForm.reset();
                            userModal.querySelector('h3.modal-title').textContent = 'Ubah Pengguna';
                            userForm.action = `${BASEURL}/admin/ubahPengguna`;
                            userForm.querySelector('#userId').value = user.id;
                            userForm.querySelector('#username').value = user.username;
                            userForm.querySelector('#id_pengguna').value = user.id_pengguna;
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
                        confirmDeleteLink.href = `${BASEURL}/admin/hapusPengguna/${button.dataset.id}`;
                        universalDeleteModal.style.display = 'flex';
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
                itemForm.action = `${BASEURL}/admin/tambahBarang`; 
                itemModal.style.display = 'flex';
            });
            
            // ✅ PERBAIKAN: Listener sekarang lebih spesifik untuk tabel barang
            document.querySelectorAll('#itemTable .edit-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const itemId = button.dataset.id;
                    fetch(`${BASEURL}/admin/getBarangById/${itemId}`)
                        .then(res => res.json())
                        .then(item => {
                            itemForm.reset();
                            itemModal.querySelector('h3.modal-title').textContent = 'Ubah Barang';
                            itemForm.action = `${BASEURL}/admin/ubahBarang`;
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
                        confirmDeleteLink.href = `${BASEURL}/admin/hapusBarang/${button.dataset.id}`;
                        universalDeleteModal.style.display = 'flex';
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
                    kelasForm.action = `${BASEURL}/admin/tambahKelas`;
                    kelasModalTitle.textContent = 'Tambah Kelas';
                    kelasModal.style.display = 'flex';
                });
            
                document.querySelectorAll('.edit-kelas-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const id = btn.dataset.id;
                        fetch(`${BASEURL}/admin/getKelasById/${id}`)
                            .then(res => res.json())
                            .then(data => {
                                kelasForm.reset();
                                kelasForm.action = `${BASEURL}/admin/ubahKelas`;
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
                        confirmDelete.href = `${BASEURL}/admin/hapusKelas/${id}`;
                        universalDeleteModal.style.display = 'flex';
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
                    guruForm.action = `${BASEURL}/admin/tambahGuru`;
                    guruModalTitle.textContent = 'Tambah Guru';
                    guruModal.style.display = 'flex';
                });
            
                document.querySelectorAll('.edit-guru-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const id = btn.dataset.id;
                        fetch(`${BASEURL}/admin/getGuruById/${id}`)
                            .then(res => res.json())
                            .then(data => {
                                guruForm.reset();
                                guruForm.action = `${BASEURL}/admin/ubahGuru`;
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
                        confirmDelete.href = `${BASEURL}/admin/hapusGuru/${id}`;
                        universalDeleteModal.style.display = 'flex';
                    });
                });

                guruModal.querySelector('.close-button').addEventListener('click', () => guruModal.style.display = 'none');
                window.addEventListener('click', (e) => {
                    if (e.target === guruModal) guruModal.style.display = 'none';
                });
            }
        }
    }

    // ============================================================
    // LOGIKA UNTUK MANAJEMEN SISWA DI HALAMAN DETAIL KELAS
    // ============================================================
    const detailKelasPage = document.querySelector('.manajemen-siswa-container');
    if (detailKelasPage) {
        const siswaModalElement = document.getElementById('siswaModal');
        const addSiswaBtn = document.getElementById('addSiswaBtn');
        const confirmDelete = document.getElementById('confirmDelete');
        const deleteModal = document.getElementById('deleteModal');
        const pathParts = window.location.pathname.split('/');
        const currentKelasId = pathParts[pathParts.indexOf('detailKelas') + 1];

        const openSiswaModal = () => {
            siswaModalElement.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        };
        const closeSiswaModal = () => {
            siswaModalElement.style.display = 'none';
            document.body.style.overflow = 'auto';
        };

        addSiswaBtn.addEventListener('click', () => {
            siswaModalElement.innerHTML = `
                <div class="modal-content" style="max-width: 700px;">
                    <span class="close-button">&times;</span>
                    <h3>Tambah Siswa Baru</h3>
                    <form id="siswaForm" action="${BASEURL}/admin/tambahSiswa" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="kelas_id" value="${currentKelasId}">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nama">Nama Siswa</label>
                                <input type="text" id="nama" name="nama" required>
                            </div>
                            <div class="form-group">
                                <label for="id_siswa">ID Siswa (NIS/NISN)</label>
                                <input type="text" id="id_siswa" name="id_siswa" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="jenis_kelamin">Jenis Kelamin</label>
                                <select id="jenis_kelamin" name="jenis_kelamin" required>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="status">Status di Kelas</label>
                                <select id="status" name="status" required>
                                    <option value="Murid">Murid</option>
                                    <option value="Ketua Murid">Ketua Murid</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                             <div class="form-group">
                                <label for="ttl">Tempat, Tanggal Lahir</label>
                                <input type="text" id="ttl" name="ttl" placeholder="Contoh: Bandung, 2005-08-17">
                            </div>
                            <div class="form-group">
                                <label for="agama">Agama</label>
                                <input type="text" id="agama" name="agama">
                            </div>
                        </div>
                         <div class="form-row">
                            <div class="form-group">
                                <label for="no_hp">No. HP</label>
                                <input type="text" id="no_hp" name="no_hp">
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="alamat">Alamat</label>
                            <textarea id="alamat" name="alamat" rows="2"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="foto">Foto Siswa</label>
                            <input type="file" id="foto" name="foto" accept="image/*">
                        </div>
                        <button type="submit">Simpan</button>
                    </form>
                </div>`;
            openSiswaModal();
            siswaModalElement.querySelector('.close-button').addEventListener('click', closeSiswaModal);
        });

        document.querySelectorAll('.edit-siswa-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const siswaId = btn.dataset.id;
                fetch(`${BASEURL}/admin/getSiswaById/${siswaId}`)
                    .then(res => res.json())
                    .then(data => {
                        siswaModalElement.innerHTML = `
                            <div class="modal-content" style="max-width: 700px;">
                                <span class="close-button">&times;</span>
                                <h3>Ubah Data Siswa</h3>
                                <form id="siswaForm" action="${BASEURL}/admin/ubahSiswa" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="${data.id}">
                                    <input type="hidden" name="kelas_id" value="${currentKelasId}">
                                    <div class="form-row">
                                        <div class="form-group"><label for="nama">Nama Siswa</label><input type="text" id="nama" name="nama" value="${data.nama}" required></div>
                                        <div class="form-group"><label for="id_siswa">ID Siswa (NIS/NISN)</label><input type="text" id="id_siswa" name="id_siswa" value="${data.id_siswa}" required></div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group"><label for="jenis_kelamin">Jenis Kelamin</label><select id="jenis_kelamin" name="jenis_kelamin" required></select></div>
                                        <div class="form-group"><label for="status">Status di Kelas</label><select id="status" name="status" required></select></div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group"><label for="ttl">Tempat, Tanggal Lahir</label><input type="text" id="ttl" name="ttl" value="${data.ttl || ''}"></div>
                                        <div class="form-group"><label for="agama">Agama</label><input type="text" id="agama" name="agama" value="${data.agama || ''}"></div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group"><label for="no_hp">No. HP</label><input type="text" id="no_hp" name="no_hp" value="${data.no_hp || ''}"></div>
                                        <div class="form-group"><label for="email">Email</label><input type="email" id="email" name="email" value="${data.email || ''}"></div>
                                    </div>
                                    <div class="form-group"><label for="alamat">Alamat</label><textarea id="alamat" name="alamat" rows="2">${data.alamat || ''}</textarea></div>
                                    <div class="form-group"><label for="foto">Ganti Foto (Opsional)</label><input type="file" id="foto" name="foto" accept="image/*"></div>
                                    <button type="submit">Simpan Perubahan</button>
                                </form>
                            </div>`;
                        
                        const jkSelect = siswaModalElement.querySelector(`#jenis_kelamin`);
                        jkSelect.innerHTML = `<option value="Laki-laki">Laki-laki</option><option value="Perempuan">Perempuan</option>`;
                        jkSelect.value = data.jenis_kelamin;
                        
                        const statusSelect = siswaModalElement.querySelector(`#status`);
                        statusSelect.innerHTML = `<option value="Murid">Murid</option><option value="Ketua Murid">Ketua Murid</option>`;
                        statusSelect.value = data.status;

                        openSiswaModal();
                        siswaModalElement.querySelector('.close-button').addEventListener('click', closeSiswaModal);
                    });
            });
        });

        // ✅ PERBAIKAN: Memastikan tombol hapus siswa berfungsi
        document.querySelectorAll('.delete-siswa-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const siswaId = btn.dataset.id;
                // Pastikan `confirmDelete` dari scope universal bisa diakses
                if (confirmDelete) {
                    confirmDelete.href = `${BASEURL}/admin/hapusSiswa/${siswaId}/${currentKelasId}`;
                    deleteModal.style.display = 'flex';
                }
            });
        });

        window.addEventListener('click', (e) => {
            if (e.target === siswaModalElement) closeSiswaModal();
        });
    }
    // ✅ KODE BARU: Logika untuk modal bukti pengembalian
const buktiModal = document.getElementById('buktiModal');
if (buktiModal) {
    const buktiModalImg = document.getElementById('buktiModalImg');
    const closeBtn = buktiModal.querySelector('.close-button');

    document.querySelectorAll('.view-bukti-btn').forEach(button => {
        button.addEventListener('click', function() {
            const imgUrl = this.dataset.imgUrl;
            buktiModalImg.src = imgUrl;
            buktiModal.style.display = 'flex';
        });
    });

    const closeModal = () => {
        buktiModal.style.display = 'none';
        buktiModalImg.src = ''; // Kosongkan src saat ditutup
    };

    closeBtn.addEventListener('click', closeModal);
    window.addEventListener('click', (event) => {
        if (event.target == buktiModal) {
            closeModal();
        }
    });
}
});

