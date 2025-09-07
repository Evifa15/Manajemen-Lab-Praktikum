<?php

class GuruController {

    private function checkAuth() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'guru') {
            Flasher::setFlash('Akses ditolak!', 'Anda harus login sebagai guru.', 'danger');
            header('Location: ' . BASEURL . '/login?role=guru');
            exit;
        }
    }

    public function index() {
        $this->checkAuth();
        $data = [ 'title' => 'Dashboard Guru', 'username' => $_SESSION['username'] ];
        $this->view('guru/index', $data);
    }

    public function verifikasiPeminjaman() {
        $this->checkAuth();
        $data = [ 'title' => 'Verifikasi Peminjaman', 'username' => $_SESSION['username'] ];
        $this->view('guru/verifikasi_peminjaman', $data);
    }
    
    public function daftarSiswaWali() {
        $this->checkAuth();
        $data = [ 'title' => 'Daftar Siswa Wali', 'username' => $_SESSION['username'] ];
        $this->view('guru/daftar_siswa_wali', $data);
    }

    public function riwayatPeminjaman() {
        $this->checkAuth();
        $data = [ 'title' => 'Riwayat Peminjaman', 'username' => $_SESSION['username'] ];
        $this->view('guru/riwayat_peminjaman', $data);
    }

    public function profile() {
        $this->checkAuth();
        $userModel = new User_model();
        $profileModel = new Profile_model();

        $data['user'] = $userModel->getUserById($_SESSION['user_id']);
        $data['profile'] = $profileModel->getProfileByRoleAndUserId($_SESSION['role'], $_SESSION['user_id']);
        $data['title'] = 'Profil Saya';
        
        // ✅ PERBAIKAN: Memuat header dan footer yang benar untuk guru
        extract($data);
        require_once '../app/views/layouts/guru_header.php';
        require_once '../app/views/admin/profile.php'; // Tetap menggunakan view profile yang sama
        require_once '../app/views/layouts/guru_footer.php';
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

    public function view($view, $data = []) {
        extract($data);
        require_once '../app/views/layouts/guru_header.php';
        require_once '../app/views/' . $view . '.php';
        require_once '../app/views/layouts/guru_footer.php';
    }
}