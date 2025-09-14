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

    public function verifikasiPeminjaman($halaman = 1) {
    $this->checkAuth();
    $peminjamanModel = new Peminjaman_model();
    $guruModel = new Guru_model();

    $halaman = max(1, (int)$halaman);
    $limit = 10;
    $offset = ($halaman - 1) * $limit;

    $guru = $guruModel->getGuruByUserId($_SESSION['user_id']);
    $requests = [];
    $totalHalaman = 1;
    $keyword = $_GET['search'] ?? null;

    if ($guru && isset($guru['id'])) {
        $requests = $peminjamanModel->getPeminjamanForVerification($guru['id'], $offset, $limit, $keyword);
        $totalRequests = $peminjamanModel->countAllVerificationRequests($guru['id'], $keyword);
        $totalHalaman = ceil($totalRequests / $limit);
    } else {
        Flasher::setFlash('Peringatan!', 'Data profil guru Anda tidak lengkap. Silakan hubungi Administrator.', 'danger');
    }

    $data = [ 
        'title' => 'Verifikasi Peminjaman', 
        'username' => $_SESSION['username'],
        'requests' => $requests,
        'halaman_aktif' => $halaman,
        'total_halaman' => $totalHalaman,
        'keyword' => $keyword
    ];

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
        
        extract($data);
        require_once '../app/views/layouts/guru_header.php';
        require_once '../app/views/admin/profile.php';
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
    public function prosesVerifikasi() {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['peminjaman_id']) && isset($_POST['status'])) {
            $peminjamanModel = new Peminjaman_model();
            $barangModel = new Barang_model();
            $peminjamanId = $_POST['peminjaman_id'];
            $status = $_POST['status'];
            $keterangan = $_POST['keterangan'] ?? null;
            
            $peminjaman = $peminjamanModel->getPeminjamanById($peminjamanId);

            if ($peminjaman && $peminjaman['status'] == 'Menunggu Verifikasi') {
                if ($status === 'Ditolak' && empty($keterangan)) {
                    Flasher::setFlash('Gagal!', 'Alasan penolakan tidak boleh kosong.', 'danger');
                } else {
                    $result = $peminjamanModel->updatePeminjamanStatusAndKeterangan($peminjamanId, $status, $keterangan);
                    
                    if ($result > 0) {
                        if ($status === 'Disetujui') {
                            $barangModel->kurangiStok($peminjaman['barang_id'], $peminjaman['jumlah_pinjam']);
                            Flasher::setFlash('Berhasil!', 'Peminjaman berhasil disetujui dan stok barang telah dikurangi.', 'success');
                        } else if ($status === 'Ditolak') {
                            Flasher::setFlash('Berhasil!', 'Peminjaman berhasil ditolak.', 'success');
                        }
                    } else {
                        Flasher::setFlash('Gagal!', 'Terjadi kesalahan saat memperbarui status.', 'danger');
                    }
                }
            } else {
                 Flasher::setFlash('Gagal!', 'Permintaan peminjaman tidak valid atau sudah diproses.', 'danger');
            }
        } else {
            Flasher::setFlash('Gagal!', 'Permintaan tidak valid.', 'danger');
        }
        header('Location: ' . BASEURL . '/guru/verifikasi');
        exit;
    }
}