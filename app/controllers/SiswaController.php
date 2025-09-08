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
        $siswaModel = new Siswa_model(); // Tambahkan model siswa
        
        $halaman = max(1, (int)$halaman);
        $limit = 15;
        $offset = ($halaman - 1) * $limit;
        
        $filters = [
            'keyword' => $_GET['search'] ?? null,
            'ketersediaan' => $_GET['filter_ketersediaan'] ?? null
        ];
        
        $items = $barangModel->getBarangPaginated($offset, $limit, $filters);
        $totalBarang = $barangModel->countAllBarang($filters);
        $totalHalaman = ceil($totalBarang / $limit);

        // Ambil data detail barang yang ada di keranjang
        $keranjang_ids = $_SESSION['keranjang'] ?? [];
        $data_keranjang = $barangModel->getBarangByIds($keranjang_ids);
        
        // Ambil data siswa yang sedang login
        $data_siswa = $siswaModel->getSiswaByUserId($_SESSION['user_id']);

        $data = [
            'title' => 'Katalog Barang',
            'username' => $_SESSION['username'],
            'items' => $items,
            'total_halaman' => $totalHalaman,
            'halaman_aktif' => $halaman,
            'filters' => $filters,
            'jumlah_keranjang' => count($keranjang_ids),
            'data_keranjang' => $data_keranjang,
            'data_siswa' => $data_siswa // Kirim data siswa ke view
        ];
        
        $this->view('siswa/katalog_barang', $data);
    }

    /**
     * Memproses penambahan barang ke keranjang (session).
     */
    public function tambahKeKeranjang() {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['barang_id'])) {
            $barangModel = new Barang_model();
            $barang_id = $_POST['barang_id'];

            if (!isset($_SESSION['keranjang'])) {
                $_SESSION['keranjang'] = [];
            }

            $barang = $barangModel->getBarangById($barang_id);

            if ($barang && $barang['jumlah'] > 0) {
                if (!in_array($barang_id, $_SESSION['keranjang'])) {
                    $_SESSION['keranjang'][] = $barang_id;
                    Flasher::setFlash('Berhasil!', 'Barang ditambahkan ke keranjang.', 'success');
                } else {
                    Flasher::setFlash('Info!', 'Barang sudah ada di keranjang.', 'danger');
                }
            } else {
                Flasher::setFlash('Gagal!', 'Stok barang sudah habis.', 'danger');
            }
        }
        header('Location: ' . BASEURL . '/siswa/katalog');
        exit;
    }
    
    /**
     * Menghapus item dari keranjang (session).
     */
    public function hapusDariKeranjang($id) {
        $this->checkAuth();
        if (isset($_SESSION['keranjang'])) {
            $key = array_search($id, $_SESSION['keranjang']);
            if ($key !== false) {
                unset($_SESSION['keranjang'][$key]);
                Flasher::setFlash('Berhasil!', 'Item dihapus dari keranjang.', 'success');
            }
        }
        header('Location: ' . BASEURL . '/siswa/katalog');
        exit;
    }

    /**
     * Memproses semua item di keranjang untuk diajukan sebagai peminjaman.
     */
    public function prosesPeminjaman() {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Logika untuk menyimpan data peminjaman ke database akan dibuat di sini.
            // Termasuk validasi, pengecekan stok ulang, dan transaksi database.

            // Untuk sekarang, kita kosongkan keranjang dan beri notifikasi.
            unset($_SESSION['keranjang']);
            Flasher::setFlash('Berhasil!', 'Pengajuan Anda telah dikirim dan sedang menunggu verifikasi.', 'success');
            header('Location: ' . BASEURL . '/siswa/riwayat');
            exit;
        } else {
            header('Location: ' . BASEURL . '/siswa/katalog');
            exit;
        }
    }

    /**
     * Menampilkan halaman pengembalian barang.
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
        
        extract($data);
        require_once '../app/views/layouts/siswa_header.php';
        require_once '../app/views/admin/profile.php';
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
     */
    public function view($view, $data = []) {
        extract($data);
        require_once '../app/views/layouts/siswa_header.php';
        require_once '../app/views/' . $view . '.php';
        require_once '../app/views/layouts/siswa_footer.php';
    }
}
