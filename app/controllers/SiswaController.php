<?php

class SiswaController {

    // Hapus atau kosongkan fungsi __construct() dari sini

    /**
     * Fungsi ini akan kita panggil di setiap method untuk memeriksa
     * apakah pengguna berhak mengakses halaman siswa.
     */
    private function checkAuth() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'siswa') {
            Flasher::setFlash('Akses ditolak!', 'Anda harus login sebagai siswa.', 'danger');
            header('Location: '. BASEURL . '/login?role=siswa');
            exit;
        }
    }

    public function index() {
        $this->checkAuth(); // Panggil pemeriksaan keamanan di sini
        $data = [ 'title' => 'Dashboard Siswa', 'username' => $_SESSION['username'] ];
        $this->view('siswa/index', $data);
    }

    public function katalogBarang() {
        $this->checkAuth(); // Panggil pemeriksaan keamanan di sini
        $data = [ 'title' => 'Katalog Barang', 'username' => $_SESSION['username'] ];
        $this->view('siswa/katalog_barang', $data);
    }
    
    public function pengembalianBarang() {
        $this->checkAuth(); // Panggil pemeriksaan keamanan di sini
        $data = [ 'title' => 'Pengembalian Barang', 'username' => $_SESSION['username'] ];
        $this->view('siswa/pengembalian_barang', $data);
    }

    public function riwayatPeminjaman() {
        $this->checkAuth(); // Panggil pemeriksaan keamanan di sini
        $data = [ 'title' => 'Riwayat Peminjaman', 'username' => $_SESSION['username'] ];
        $this->view('siswa/riwayat_peminjaman', $data);
    }
public function profile() {
        $this->checkAuth();
        $userModel = new User_model();
        $profileModel = new Profile_model();

        $data['user'] = $userModel->getUserById($_SESSION['user_id']);
        $data['profile'] = $profileModel->getProfileByRoleAndUserId($_SESSION['role'], $_SESSION['user_id']);
        $data['title'] = 'Profil Saya';
        
        // ✅ PERBAIKAN: Path sekarang mengarah ke admin/profile.php
        extract($data);
        require_once '../app/views/layouts/admin_header.php';
        require_once '../app/views/admin/profile.php'; 
        require_once '../app/views/layouts/admin_footer.php';
    }

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
        header('Location: ' . BASEURL . '/guru/profile');
        exit;
    }
    // Helper untuk memuat view DENGAN layout siswa
    public function view($view, $data = []) {
        extract($data);
        require_once '../app/views/layouts/siswa_header.php';
        // --- PERBAIKAN: Baris yang hilang ditambahkan di sini ---
        require_once '../app/views/' . $view . '.php';
        // ----------------------------------------------------
        require_once '../app/views/layouts/siswa_footer.php';
    }
}

