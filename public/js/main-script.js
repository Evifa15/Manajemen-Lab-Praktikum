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
        
        // Logika sidebar tetap di sini karena dibutuhkan oleh semua peran
        const adminNavs = { 'dashboard': 'nav-dashboard', 'pengguna': 'nav-pengguna', 'barang': 'nav-barang', 'kelas': 'nav-kelas', 'laporan': 'nav-laporan', 'profile': 'nav-profile' };
        const guruNavs = { 'dashboard': 'nav-dashboard-guru', 'verifikasi': 'nav-verifikasi-guru', 'siswa': 'nav-siswa-guru', 'riwayat': 'nav-riwayat-guru', 'profile': 'nav-profile-guru' };
        const siswaNavs = { 'dashboard': 'nav-dashboard-siswa', 'katalog': 'nav-katalog-siswa', 'pengembalian': 'nav-pengembalian-siswa', 'riwayat': 'nav-riwayat-siswa' };

        setActiveLink('admin', adminNavs);
        setActiveLink('guru', guruNavs);
        setActiveLink('siswa', siswaNavs);

        // Modal hapus universal bisa digunakan oleh peran lain, jadi tetap di sini
        const universalDeleteModal = document.getElementById('deleteModal');
        if (universalDeleteModal) {
            const cancelBtn = universalDeleteModal.querySelector('#cancelDeleteBtn, #cancelDelete');
            const closeBtn = universalDeleteModal.querySelector('.close-button');
            const closeModal = () => { universalDeleteModal.style.display = 'none'; };
            if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
            if (closeBtn) closeBtn.addEventListener('click', closeModal);
            window.addEventListener('click', (e) => { if (e.target === universalDeleteModal) closeModal(); });
        }
    }

    // ===================================================
    // LOGIKA UNTUK HALAMAN KATALOG SISWA
    // ===================================================
    const katalogGrid = document.querySelector('.katalog-grid');
    if (katalogGrid) {
        const tombolKeranjang = document.querySelectorAll('.btn-keranjang');

        tombolKeranjang.forEach(tombol => {
            tombol.addEventListener('click', function() {
                const card = this.closest('.katalog-card');
                const namaBarang = card.querySelector('.katalog-title').textContent;
                alert(`"${namaBarang}" telah ditambahkan ke keranjang! (Fungsionalitas penuh akan dikembangkan)`);
            });
        });

        console.log('Skrip untuk katalog berhasil dimuat.');
    }
});