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
            'dashboard': 'nav-dashboard',
            'pengguna': 'nav-pengguna',
            'barang': 'nav-barang',
            'kelas': 'nav-kelas',
            'laporan': 'nav-laporan',
            'profile': 'nav-profile'
        };

        const guruNavs = {
            'dashboard': 'nav-dashboard-guru',
            'verifikasi': 'nav-verifikasi-guru',
            'siswa': 'nav-siswa-guru',
            'riwayat': 'nav-riwayat-guru',
            'profile': 'nav-profile-guru'
        };

        const siswaNavs = {
            'dashboard': 'nav-dashboard-siswa',
            'katalog': 'nav-katalog-siswa',
            'pengembalian': 'nav-pengembalian-siswa',
            'riwayat': 'nav-riwayat-siswa'
        };

        setActiveLink('admin', adminNavs);
        setActiveLink('guru', guruNavs);
        setActiveLink('siswa', siswaNavs);

        const addUserBtn = document.getElementById('addUserBtn');
        const userModal = document.getElementById('userModal');
        const closeUserModalBtn = userModal ? userModal.querySelector('.close-button') : null;

        const formTitle = userModal ? userModal.querySelector('h3.modal-title') : null;
        const userForm = document.getElementById('userForm');
        
        const usernameInput = userForm ? document.getElementById('username') : null;
        const emailInput = userForm ? document.getElementById('email') : null;
        const idPenggunaInput = userForm ? document.getElementById('id_pengguna') : null;
        const passwordInput = userForm ? document.getElementById('password') : null;
        const roleInput = userForm ? document.getElementById('role') : null;
        const userIdInput = userForm ? document.getElementById('userId') : null;
        const submitButton = userForm ? userForm.querySelector('button[type="submit"]') : null;

        if (addUserBtn && userModal) {
            addUserBtn.addEventListener('click', () => {
                userForm.reset();
                formTitle.textContent = 'Tambah Pengguna';
                userForm.action = `${BASEURL}/admin/tambah-pengguna`;
                passwordInput.required = true;
                
                userModal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            });
            
            if (closeUserModalBtn) {
                closeUserModalBtn.addEventListener('click', () => {
                    userModal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                });
            }

            window.addEventListener('click', (event) => {
                if (event.target === userModal) {
                    userModal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
            });
        }

        const deleteModal = document.getElementById('deleteModal');
        const confirmDeleteLink = document.getElementById('confirmDelete');
        const cancelDeleteBtn = document.getElementById('cancelDelete');
        const closeDeleteModalBtn = deleteModal ? deleteModal.querySelector('.close-button') : null;
        
        const deleteButtons = document.querySelectorAll('.delete-btn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                const userId = button.getAttribute('data-id');
                confirmDeleteLink.href = `${BASEURL}/admin/hapus-pengguna/${userId}`;
                deleteModal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            });
        });

        if (cancelDeleteBtn) {
            cancelDeleteBtn.addEventListener('click', () => {
                deleteModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            });
        }
        
        if (closeDeleteModalBtn) {
            closeDeleteModalBtn.addEventListener('click', () => {
                deleteModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            });
        }

        window.addEventListener('click', (event) => {
            if (event.target === deleteModal) {
                deleteModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
        
        const editButtons = document.querySelectorAll('.edit-btn');
        editButtons.forEach(button => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                const userId = button.getAttribute('data-id');

                fetch(`${BASEURL}/admin/get-pengguna-by-id/${userId}`)
                    .then(response => response.json())
                    .then(user => {
                        formTitle.textContent = 'Ubah Pengguna';
                        submitButton.textContent = 'Ubah Data';
                        userForm.action = `${BASEURL}/admin/ubah-pengguna`;
                        
                        userIdInput.value = user.id;

                        usernameInput.value = user.username;
                        emailInput.value = user.email;
                        idPenggunaInput.value = user.id_pengguna;
                        roleInput.value = user.role;
                        
                        passwordInput.value = '';
                        passwordInput.required = false;

                        userModal.style.display = 'flex';
                        document.body.style.overflow = 'hidden';
                    })
                    .catch(error => console.error('Error fetching user data:', error));
            });
        });

        // ======================================
        // LOGIKA PENCARIAN PENGGUNA (ASLI)
        // ======================================
        const searchInput = document.getElementById('searchInput');
        const userTableBody = document.querySelector('#userTable tbody');

        if (searchInput && userTableBody) {
            searchInput.addEventListener('keyup', (event) => {
                const searchTerm = event.target.value.toLowerCase();
                const tableRows = userTableBody.querySelectorAll('tr');

                tableRows.forEach(row => {
                    const rowText = row.textContent.toLowerCase();
                    
                    if (rowText.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    }
});