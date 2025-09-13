<?php
// =================================================================
// INITIALIZER & BOOTSTRAP
// =================================================================
// Memulai session jika belum ada
if (!session_id()) { 
    session_start(); 
}

// Memuat semua file inti yang dibutuhkan oleh aplikasi
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
require_once '../app/models/Profile_model.php';
require_once '../app/models/Staff_model.php';

// =================================================================
// PARSING URL
// =================================================================
// Inisialisasi semua controller yang akan digunakan
$authController = new AuthController();
$adminController = new AdminController();
$guruController = new GuruController();
$siswaController = new SiswaController();

// Mengambil URL yang bersih dari parameter 'url' yang dikirim oleh .htaccess
$url = isset($_GET['url']) ? $_GET['url'] : '';

// Memecah URL menjadi segmen-segmen: controller/method/param1/param2
$url_parts = explode('/', filter_var(rtrim($url, '/'), FILTER_SANITIZE_URL));

// Menentukan controller, method, dan parameter dari segmen URL
$controller = $url_parts[0] ?? '';
$method = $url_parts[1] ?? '';
$param1 = $url_parts[2] ?? '';
$param2 = $url_parts[3] ?? '';

// =================================================================
// RUTE OTENTIKASI (LOGIN, LOGOUT, & PEMILIHAN PERAN)
// =================================================================
if (empty($controller)) {
    $authController->showPilihPeran(); // Halaman awal
} elseif ($controller === 'login') {
    $authController->showLogin(); // Halaman form login
} elseif ($controller === 'process-login') {
    $authController->processLogin(); // Proses data login
} elseif ($controller === 'logout') {
    $authController->logout(); // Proses logout
} 

// =================================================================
// RUTE UNTUK PENGGUNA DENGAN PERAN "ADMIN"
// =================================================================
elseif ($controller === 'admin') {
    // --- Dashboard Admin ---
    if ($method === 'dashboard' || empty($method)) {
        $adminController->index();
    } 
    
    // --- Manajemen Pengguna ---
    elseif ($method === 'pengguna') { $adminController->manajemenPengguna($param1, $param2); } 
    elseif ($method === 'tambah-pengguna') { $adminController->tambahPengguna(); }
    elseif ($method === 'ubah-pengguna') { $adminController->ubahPengguna(); }
    elseif ($method === 'hapus-pengguna' && !empty($param1)) { $adminController->hapusPengguna($param1); }
    elseif ($method === 'get-pengguna-detail-by-id' && !empty($param1)) { $adminController->getPenggunaDetailById($param1); }
    elseif ($method === 'tambah-staff') { $adminController->tambahStaff(); }
    elseif ($method === 'import-staff') { $adminController->importStaff(); }
    elseif ($method === 'searchStaff') { $adminController->searchStaff(); }
    elseif ($method === 'hapus-staff' && !empty($param1)) { $adminController->hapusStaff($param1); }
    elseif ($method === 'get-staff-by-id' && !empty($param1)) { $adminController->getStaffById($param1); }
    elseif ($method === 'ubah-staff') { $adminController->ubahStaff(); }
    elseif ($method === 'hapus-staff-massal') { $adminController->hapusStaffMassal(); }
    elseif ($method === 'import-guru') { $adminController->importGuru(); }
    elseif ($method === 'searchGuru') { $adminController->searchGuru(); }
    elseif ($method === 'hapus-guru' && !empty($param1)) { $adminController->hapusGuru($param1); }
    elseif ($method === 'get-guru-by-id' && !empty($param1)) { $adminController->getGuruById($param1); }
    elseif ($method === 'ubah-guru') { $adminController->ubahGuru(); }
    elseif ($method === 'detailGuru' && !empty($param1)) { $adminController->detailGuru($param1); }
    elseif ($method === 'hapus-guru-massal') { $adminController->hapusGuruMassal(); }
    elseif ($method === 'detailStaff' && !empty($param1)) { $adminController->detailStaff($param1); }
    elseif ($method === 'assignSiswaToKelas') { $adminController->assignSiswaToKelas(); } 
    elseif ($method === 'hapusSiswaDariKelas' && !empty($param1)) { $adminController->hapusSiswaDariKelas($param1); }
    elseif ($method === 'updateSiswaStatus') { $adminController->updateSiswaStatus(); }
    // --- Manajemen Siswa ---
    elseif ($method === 'tambah-siswa') { $adminController->tambahSiswa(); }
    elseif ($method === 'ubah-siswa') { $adminController->ubahSiswa(); }
    elseif ($method === 'hapus-siswa' && !empty($param1)) { $adminController->hapusSiswa($param1); }
    elseif ($method === 'get-siswa-by-id' && !empty($param1)) { $adminController->getSiswaById($param1); }
    elseif ($method === 'hapus-siswa-massal') { $adminController->hapusSiswaMassal(); }
    elseif ($method === 'import-siswa') { $adminController->importSiswa(); }
    elseif ($method === 'detailSiswa' && !empty($param1)) { $adminController->detailSiswa($param1); }
    // --- Manajemen Barang ---
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
elseif ($method === 'import-barang') { $adminController->importBarang(); } 
    // --- Import & Hapus Massal ---
    elseif ($method === 'import-kelas') { $adminController->importKelas(); }
    elseif ($method === 'import-guru') { $adminController->importGuru(); }
    elseif ($method === 'hapus-kelas-massal') { $adminController->hapusKelasMassal(); }
    elseif ($method === 'hapus-guru-massal') { $adminController->hapusGuruMassal(); }

    // --- Manajemen Kelas & Guru ---
   elseif ($method === 'kelas') { $halaman = $param1 ?: 1; $adminController->manajemenKelas($halaman); }
    elseif ($method === 'tambah-kelas') { $adminController->tambahKelas(); }
    elseif ($method === 'get-kelas-by-id' && !empty($param1)) { $adminController->getKelasById($param1); }
    elseif ($method === 'ubah-kelas') { $adminController->ubahKelas(); }
    elseif ($method === 'hapus-kelas' && !empty($param1)) { $adminController->hapusKelas($param1); }
    elseif ($method === 'detailKelas' && !empty($param1)) { 
        $halaman = $param2 ?? 1;
        $adminController->detailKelas($param1, $halaman); 
    }
    elseif ($method === 'tambah-guru') { $adminController->tambahGuru(); }
    elseif ($method === 'get-guru-by-id' && !empty($param1)) { $adminController->getGuruById($param1); }
    elseif ($method === 'detailGuru' && !empty($param1)) { $adminController->detailGuru($param1); }
    elseif ($method === 'ubah-password-akun') { $adminController->ubahPasswordAkun(); }
    elseif ($method === 'ubah-guru') { $adminController->ubahGuru(); }
    elseif ($method === 'hapus-guru' && !empty($param1)) { $adminController->hapusGuru($param1); }
    elseif ($method === 'detailStaff' && !empty($param1)) { $adminController->detailStaff($param1); }
    elseif ($method === 'getStaffById' && !empty($param1)) { $adminController->getStaffById($param1); }
    elseif ($method === 'ubah-staff') { $adminController->ubahStaff(); }
    elseif ($method === 'hapus-staff' && !empty($param1)) { $adminController->hapusStaff($param1); }
    elseif ($method === 'tambah-staff') { $adminController->tambahStaff(); }
    elseif ($method === 'detailStaff' && !empty($param1)) { $adminController->detailStaff($param1); }
    elseif ($method === 'laporan') {
        $halaman = $param1 ?? 1;
        $adminController->laporanRiwayat($halaman);
    } 
    elseif ($method === 'unduh-laporan') { $adminController->unduhLaporan(); }

    // --- Manajemen Profil Admin ---
    elseif ($method === 'profile') {
        $adminController->profile();
    } 
    elseif ($method === 'change-password') {
        $adminController->changePassword();
    } 
    
    // --- Penanganan jika method Admin tidak ditemukan ---
    else {
        http_response_code(404);
        echo "<h1>404 Not Found</h1> Halaman yang Anda cari tidak ditemukan di dalam Admin.";
    }
}
// =================================================================
// RUTE UNTUK PENGGUNA DENGAN PERAN "GURU"
// =================================================================
elseif ($controller === 'guru') {
    if ($method === 'dashboard' || empty($method)) {
        $guruController->index();
    } elseif ($method === 'verifikasi') {
        $guruController->verifikasiPeminjaman();
    } elseif ($method === 'proses-verifikasi') {
        $guruController->prosesVerifikasi();
    } elseif ($method === 'siswa') {
        $guruController->daftarSiswaWali();
    } elseif ($method === 'riwayat') {
        $guruController->riwayatPeminjaman();
    } 
    elseif ($method === 'profile') {
        $guruController->profile();
    }
    elseif ($method === 'change-password') {
        $guruController->changePassword();
    }
    else {
        http_response_code(404);
        echo "<h1>404 Not Found</h1> Halaman yang Anda cari tidak ditemukan di dalam Guru.";
    }
}
// =================================================================
// RUTE UNTUK PENGGUNA DENGAN PERAN "SISWA"
// =================================================================
elseif ($controller === 'siswa') {
    if ($method === 'dashboard' || empty($method)) {
        $siswaController->index();
    } elseif ($method === 'katalog') {
        $halaman = $param1 ?? 1;
        $siswaController->katalogBarang($halaman);
    } 
    elseif ($method === 'pengembalian') {
        $siswaController->pengembalianBarang();
    } elseif ($method === 'riwayat') {
        $halaman = $param1 ?? 1;
        $siswaController->riwayatPeminjaman($halaman);
    } 
    elseif ($method === 'tambah-ke-keranjang') {
        $siswaController->tambahKeKeranjang();
    }
    elseif ($method === 'hapus-dari-keranjang' && !empty($param1)) {
        $siswaController->hapusDariKeranjang($param1);
    }
    elseif ($method === 'proses-peminjaman') {
        $siswaController->prosesPeminjaman();
    }
    elseif ($method === 'profile') {
        $siswaController->profile();
    }
    elseif ($method === 'change-password') {
        $siswaController->changePassword();
    }
    else {
        http_response_code(404);
        echo "<h1>404 Not Found</h1> Halaman yang Anda cari tidak ditemukan di dalam Siswa.";
    }
} 
// =================================================================
// PENANGANAN JIKA CONTROLLER TIDAK DITEMUKAN
// =================================================================
else {
    http_response_code(404);
    echo "<h1>404 Not Found</h1> Halaman yang Anda cari tidak ditemukan.";
}