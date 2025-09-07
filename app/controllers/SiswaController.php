<?php

class SiswaController {

    /**
     * Memeriksa apakah pengguna sudah login dan memiliki peran sebagai 'siswa'.
     * Jika tidak, akan diarahkan ke halaman login.
     */
    private function checkAuth() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'siswa') {
            Flasher::setFlash('Akses ditolak!', 'Anda harus login sebagai siswa.', 'danger');
            header('Location: '. BASEURL . '/login?role=siswa');
            exit;
        }
    }

    /**
     * Menampilkan halaman dashboard utama untuk siswa.
     */
    public function index() {
        $this->checkAuth();
        $data = [
            'title' => 'Dashboard Siswa',
            'username' => $_SESSION['username']
        ];
        $this->view('siswa/index', $data);
    }

    /**
     * Menampilkan halaman katalog barang dengan data dinamis dan pagination.
     * @param int $halaman Nomor halaman saat ini.
     */
    public function katalogBarang($halaman = 1) {
        $this->checkAuth();
        $barangModel = new Barang_model();
        
        $halaman = max(1, (int)$halaman);
        $limit = 9; // Jumlah barang yang ditampilkan per halaman
        $offset = ($halaman - 1) * $limit;
        
        $items = $barangModel->getBarangPaginated($offset, $limit);
        $totalBarang = $barangModel->countAllBarang();
        $totalHalaman = ceil($totalBarang / $limit);

        $data = [
            'title' => 'Katalog Barang',
            'username' => $_SESSION['username'],
            'items' => $items,
            'total_halaman' => $totalHalaman,
            'halaman_aktif' => $halaman
        ];
        
        $this->view('siswa/katalog_barang', $data);
    }

    /**
     * Memproses pengajuan peminjaman barang oleh siswa.
     * Method ini akan dipanggil dari form di halaman katalog.
     */
    public function ajukanPeminjaman() {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['barang_id'])) {
            $barangModel = new Barang_model();
            $peminjamanModel = new Peminjaman_model();

            $barang_id = $_POST['barang_id'];
            $user_id = $_SESSION['user_id'];

            $barang = $barangModel->getBarangById($barang_id);

            // 1. Cek ketersediaan stok barang
            if ($barang && $barang['jumlah'] > 0) {
                
                // 2. Buat data untuk dimasukkan ke tabel peminjaman
                $dataPeminjaman = [
                    'user_id' => $user_id,
                    'barang_id' => $barang_id,
                    'tanggal_pinjam' => date('Y-m-d H:i:s'),
                    'status' => 'Menunggu Verifikasi' // Status awal
                ];

                // 3. Tambahkan data peminjaman dan kurangi stok
                if ($peminjamanModel->tambahPeminjaman($dataPeminjaman) > 0) {
                    $barangModel->kurangiStok($barang_id, 1);
                    Flasher::setFlash('Berhasil!', 'Pengajuan peminjaman telah dikirim.', 'success');
                } else {
                    Flasher::setFlash('Gagal!', 'Terjadi kesalahan saat mengajukan peminjaman.', 'danger');
                }
            } else {
                Flasher::setFlash('Gagal!', 'Stok barang sudah habis.', 'danger');
            }
        }
        header('Location: ' . BASEURL . '/siswa/katalog');
        exit;
    }
    
    /**
     * Menampilkan halaman pengembalian barang.
     * (Fungsionalitas backend belum diimplementasikan)
     */
    public function pengembalianBarang() {
        $this->checkAuth();
        $data = [
            'title' => 'Pengembalian Barang',
            'username' => $_SESSION['username']
        ];
        $this->view('siswa/pengembalian_barang', $data);
    }

    /**
     * Menampilkan riwayat peminjaman barang untuk siswa yang sedang login.
     * @param int $halaman Nomor halaman saat ini.
     */
    public function riwayatPeminjaman($halaman = 1) {
        $this->checkAuth();
        $peminjamanModel = new Peminjaman_model();
        
        $halaman = max(1, (int)$halaman);
        $limit = 10;
        $offset = ($halaman - 1) * $limit;
        $userId = $_SESSION['user_id'];
        
        $totalRiwayat = $peminjamanModel->countHistoryByUserId($userId);
        $totalHalaman = ceil($totalRiwayat / $limit);
        
        $data = [
            'title' => 'Riwayat Peminjaman',
            'username' => $_SESSION['username'],
            'history' => $peminjamanModel->getHistoryByUserId($userId, $offset, $limit),
            'total_halaman' => $totalHalaman,
            'halaman_aktif' => $halaman
        ];
        
        $this->view('siswa/riwayat_peminjaman', $data);
    }
    
    /**
     * Menampilkan halaman profil siswa.
     */
    public function profile() {
        $this->checkAuth();
        $userModel = new User_model();
        $profileModel = new Profile_model();

        $data['user'] = $userModel->getUserById($_SESSION['user_id']);
        $data['profile'] = $profileModel->getProfileByRoleAndUserId($_SESSION['role'], $_SESSION['user_id']);
        $data['title'] = 'Profil Saya';
        
        // Memuat view profile secara langsung
        extract($data);
        require_once '../app/views/layouts/siswa_header.php';
        require_once '../app/views/admin/profile.php'; // Sementara menggunakan view profile yang sama
        require_once '../app/views/layouts/siswa_footer.php';
    }

    /**
     * Memproses permintaan perubahan kata sandi.
     */
    public function changePassword() {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userModel = new User_model();
            $user = $userModel->getUserById($_SESSION['user_id']);

            if (password_verify($_POST['old_password'], $user['password'])) {
                if ($_POST['new_password'] === $_POST['confirm_password']) {
                    if (strlen($_POST['new_password']) >= 6) {
                        $userModel->changePassword($_SESSION['user_id'], $_POST['new_password']);
                        Flasher::setFlash('Berhasil!', 'Kata sandi telah diubah.', 'success');
                    } else {
                        Flasher::setFlash('Gagal!', 'Password baru minimal harus 6 karakter.', 'danger');
                    }
                } else {
                    Flasher::setFlash('Gagal!', 'Konfirmasi password baru tidak cocok.', 'danger');
                }
            } else {
                Flasher::setFlash('Gagal!', 'Kata sandi lama salah.', 'danger');
            }
        }
        header('Location: ' . BASEURL . '/siswa/profile');
        exit;
    }

    /**
     * Helper function untuk memuat file view beserta header dan footer.
     * @param string $view Path ke file view.
     * @param array $data Data yang akan diekstrak untuk digunakan di view.
     */
    public function view($view, $data = []) {
        extract($data);
        require_once '../app/views/layouts/siswa_header.php';
        require_once '../app/views/' . $view . '.php';
        require_once '../app/views/layouts/siswa_footer.php';
    }
}
