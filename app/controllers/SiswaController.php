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

