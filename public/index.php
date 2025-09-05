<?php
// Selalu mulai session di baris paling awal
if (!session_id()) { 
    session_start(); 
}

// Memuat file-file konfigurasi dan controller inti
require_once '../config/config.php';
require_once '../app/core/Database.php';
require_once '../app/core/Flasher.php';
require_once '../app/controllers/AuthController.php';
require_once '../app/controllers/AdminController.php';
require_once '../app/controllers/GuruController.php';
require_once '../app/controllers/SiswaController.php';

// Inisialisasi controller yang akan digunakan
$authController = new AuthController();
$adminController = new AdminController();
$guruController = new GuruController();
$siswaController = new SiswaController();

// --- Logika routing yang DIPERBAIKI ---
// Ambil URL dari parameter 'url' yang dikirim oleh .htaccess
$url = isset($_GET['url']) ? $_GET['url'] : '';

// Pecah URL menjadi bagian-bagian
$url_parts = explode('/', filter_var(rtrim($url, '/'), FILTER_SANITIZE_URL));

// Tentukan controller, method, dan parameter
$controller = isset($url_parts[0]) ? strtolower($url_parts[0]) : '';
$method = isset($url_parts[1]) ? strtolower($url_parts[1]) : '';
$param = isset($url_parts[2]) ? $url_parts[2] : '';

// Rute Otentikasi
if (empty($controller)) {
    $authController->showPilihPeran();
} elseif ($controller === 'login') {
    $authController->showLogin();
} elseif ($controller === 'process-login') {
    $authController->processLogin();
} elseif ($controller === 'logout') {
    $authController->logout();
} 
// Rute Admin
elseif ($controller === 'admin') {
    if ($method === 'dashboard' || empty($method)) {
        $adminController->index();
    } elseif ($method === 'pengguna') {
        $adminController->manajemenPengguna();
    } elseif ($method === 'tambah-pengguna') {
        $adminController->tambahPengguna();
    } elseif ($method === 'ubah-pengguna') { // Rute ubah pengguna tanpa parameter
        $adminController->ubahPengguna();
    } elseif ($method === 'hapus-pengguna' && $param !== '') {
        $adminController->hapusPengguna($param);
    } elseif ($method === 'get-pengguna-by-id' && $param !== '') {
        $adminController->getPenggunaById($param);
    } elseif ($method === 'barang') {
        $adminController->manajemenBarang();
    } elseif ($method === 'kelas') {
        $adminController->manajemenKelas();
    } elseif ($method === 'laporan') {
        $adminController->laporanRiwayat();
    } elseif ($method === 'profile') {
        $adminController->profile();
    } else {
        http_response_code(404);
        echo "<h1>404 Not Found</h1>";
        echo "Halaman yang Anda cari tidak ditemukan.";
    }
}
// Rute Guru
elseif ($controller === 'guru') {
    if ($method === 'dashboard' || empty($method)) {
        $guruController->index();
    } elseif ($method === 'verifikasi') {
        $guruController->verifikasiPeminjaman();
    } elseif ($method === 'siswa') {
        $guruController->daftarSiswaWali();
    } elseif ($method === 'riwayat') {
        $guruController->riwayatPeminjaman();
    } elseif ($method === 'profile') {
        $guruController->profile();
    } else {
        http_response_code(404);
        echo "<h1>404 Not Found</h1>";
        echo "Halaman yang Anda cari tidak ditemukan.";
    }
}
// Rute Siswa
elseif ($controller === 'siswa') {
    if ($method === 'dashboard' || empty($method)) {
        $siswaController->index();
    } elseif ($method === 'katalog') {
        $siswaController->katalogBarang();
    } elseif ($method === 'pengembalian') {
        $siswaController->pengembalianBarang();
    } elseif ($method === 'riwayat') {
        $siswaController->riwayatPeminjaman();
    } else {
        http_response_code(404);
        echo "<h1>404 Not Found</h1>";
        echo "Halaman yang Anda cari tidak ditemukan.";
    }
} else {
    http_response_code(404);
    echo "<h1>404 Not Found</h1>";
    echo "Halaman yang Anda cari tidak ditemukan.";
}
// --- Akhir logika routing yang DIPERBAIKI ---