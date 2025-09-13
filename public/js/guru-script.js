document.addEventListener('DOMContentLoaded', () => {
    console.log('guru-script.js dimuat.');

    // Fungsi untuk menandai link aktif di sidebar Guru
    function setActiveLink() {
        const currentPath = window.location.pathname;
        const navLinksConfig = {
            'dashboard': 'nav-dashboard-guru',
            'verifikasi': 'nav-verifikasi-guru',
            'siswa': 'nav-siswa-guru',
            'riwayat': 'nav-riwayat-guru',
            'profile': 'nav-profile-guru'
        };

        for (const key in navLinksConfig) {
            const element = document.getElementById(navLinksConfig[key]);
            if (element && currentPath.includes(`/guru/${key}`)) {
                element.classList.add('active-link');
            }
        }
    }

    setActiveLink();

    // Di sini Anda bisa menambahkan JavaScript lain yang hanya untuk Guru
});