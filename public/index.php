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
require_once '../app/models/User_model.php';
require_once '../app/models/Barang_model.php';
require_once '../app/models/Guru_model.php';
require_once '../app/models/Kelas_model.php';
require_once '../app/models/Siswa_model.php';
require_once '../app/models/Peminjaman_model.php';
// ✅ Tambahkan require untuk model Profile
require_once '../app/models/Profile_model.php';


// Inisialisasi controller yang akan digunakan
$authController = new AuthController();
$adminController = new AdminController();
$guruController = new GuruController();
$siswaController = new SiswaController();

// Ambil URL dari parameter 'url' yang dikirim oleh .htaccess
$url = isset($_GET['url']) ? $_GET['url'] : '';

// Pecah URL menjadi bagian-bagian
$url_parts = explode('/', filter_var(rtrim($url, '/'), FILTER_SANITIZE_URL));

// Tentukan controller, method, dan parameter
$controller = $url_parts[0] ?? '';
$method = $url_parts[1] ?? '';
$param1 = $url_parts[2] ?? '';
$param2 = $url_parts[3] ?? '';


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
    } 
    // ... (Rute-rute lain untuk admin tetap sama)
    elseif ($method === 'pengguna') { $adminController->manajemenPengguna($param1); } 
    elseif ($method === 'tambah-pengguna') { $adminController->tambahPengguna(); }
    elseif ($method === 'ubah-pengguna') { $adminController->ubahPengguna(); }
    elseif ($method === 'hapus-pengguna' && !empty($param1)) { $adminController->hapusPengguna($param1); }
    elseif ($method === 'get-pengguna-by-id' && !empty($param1)) { $adminController->getPenggunaById($param1); } 
    elseif ($method === 'barang') {
        if (isset($url_parts[2]) && $url_parts[2] === 'detail' && isset($url_parts[3])) {
            $adminController->detailBarang($url_parts[3]);
        } else {
            $halaman = $url_parts[2] ?? 1;
            $adminController->manajemenBarang($halaman);
        }
    }
    elseif ($method === 'tambah-barang') { $adminController->tambahBarang(); }
    elseif ($method === 'ubah-barang') { $adminController->ubahBarang(); }
    elseif ($method === 'hapus-barang' && !empty($param1)) { $adminController->hapusBarang($param1); }
    elseif ($method === 'get-barang-by-id' && !empty($param1)) { $adminController->getBarangById($param1); } 
    elseif ($method === 'kelas') {
        $tab = $param1 ?: 'kelas';
        $halaman = $param2 ?: 1;
        $adminController->manajemenKelas($tab, $halaman);
    }
    elseif ($method === 'tambahKelas') { $adminController->tambahKelas(); }
    elseif ($method === 'getKelasById' && !empty($param1)) { $adminController->getKelasById($param1); }
    elseif ($method === 'ubahKelas') { $adminController->ubahKelas(); }
    elseif ($method === 'hapusKelas' && !empty($param1)) { $adminController->hapusKelas($param1); }
    elseif ($method === 'tambahGuru') { $adminController->tambahGuru(); }
    elseif ($method === 'getGuruById' && !empty($param1)) { $adminController->getGuruById($param1); }
    elseif ($method === 'detailGuru' && !empty($param1)) { $adminController->detailGuru($param1); }
    elseif ($method === 'ubahGuru') { $adminController->ubahGuru(); }
    elseif ($method === 'hapusGuru' && !empty($param1)) { $adminController->hapusGuru($param1); }
    elseif ($method === 'detailKelas' && !empty($param1)) { 
        $halaman = $param2 ?? 1;
        $adminController->detailKelas($param1, $halaman); 
    }
    elseif ($method === 'tambahSiswa') { $adminController->tambahSiswa(); }
    elseif ($method === 'getSiswaById' && !empty($param1)) { $adminController->getSiswaById($param1); }
    elseif ($method === 'ubahSiswa') { $adminController->ubahSiswa(); }
    elseif ($method === 'hapusSiswa' && !empty($param1) && !empty($param2)) { $adminController->hapusSiswa($param1, $param2); }
    elseif ($method === 'detailSiswa' && !empty($param1)) { $adminController->detailSiswa($param1); }
    elseif ($method === 'laporan') {
        $halaman = $param1 ?? 1;
        $adminController->laporanRiwayat($halaman);
    } 
    elseif ($method === 'unduhLaporan') { $adminController->unduhLaporan(); }

    // ✅ RUTE BARU: Untuk Profil Admin
    elseif ($method === 'profile') {
        $adminController->profile();
    } 
    elseif ($method === 'changePassword') {
        $adminController->changePassword();
    } 
    
    else {
        http_response_code(404);
        echo "<h1>404 Not Found</h1> Halaman yang Anda cari tidak ditemukan di dalam Admin.";
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
    } 
    
    // ✅ RUTE BARU: Untuk Profil Guru
    elseif ($method === 'profile') {
        $guruController->profile();
    }
    elseif ($method === 'changePassword') {
        $guruController->changePassword();
    }

    else {
        http_response_code(404);
        echo "<h1>404 Not Found</h1> Halaman yang Anda cari tidak ditemukan di dalam Guru.";
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
    } 
    
    // ✅ RUTE BARU: Untuk Profil Siswa
    elseif ($method === 'profile') {
        $siswaController->profile();
    }
    elseif ($method === 'changePassword') {
        $siswaController->changePassword();
    }
    
    else {
        http_response_code(404);
        echo "<h1>404 Not Found</h1> Halaman yang Anda cari tidak ditemukan di dalam Siswa.";
    }
} else {
    http_response_code(404);
    echo "<h1>404 Not Found</h1> Halaman yang Anda cari tidak ditemukan.";
}

