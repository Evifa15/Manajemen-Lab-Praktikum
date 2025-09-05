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
        $data = [
            'title' => 'Profil Saya',
            'username' => $_SESSION['username'],
            'email' => $_SESSION['email'] ?? 'guru@example.com', // Placeholder
            'role' => $_SESSION['role']
        ];
        
        // Memuat view dengan layout sederhana (bukan layout guru)
        extract($data);
        require_once '../app/views/layouts/header.php';
        require_once '../app/views/guru/profile.php'; // file view profil guru
        require_once '../app/views/layouts/footer.php';
    }

    // Helper untuk memuat view DENGAN layout guru
    public function view($view, $data = []) {
        extract($data);
        require_once '../app/views/layouts/guru_header.php';
        require_once '../app/views/' . $view . '.php';
        require_once '../app/views/layouts/guru_footer.php';
    }
}