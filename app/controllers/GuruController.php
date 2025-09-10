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
        $peminjamanModel = new Peminjaman_model();
        $guruModel = new Guru_model();

        // Dapatkan data guru yang sedang login
        $guru = $guruModel->getGuruByUserId($_SESSION['user_id']);
        
        $requests = []; // Siapkan array kosong sebagai default
        
        // PERBAIKAN: Hanya jalankan query jika data guru ditemukan
        if ($guru && isset($guru['id'])) {
            // Ambil data peminjaman yang perlu diverifikasi oleh guru ini
            $requests = $peminjamanModel->getPeminjamanForVerification($guru['id']);
        } else {
            // Beri pesan jika profil guru tidak lengkap
            Flasher::setFlash('Peringatan!', 'Data profil guru Anda tidak lengkap. Silakan hubungi Administrator.', 'danger');
        }

        $data = [ 
            'title' => 'Verifikasi Peminjaman', 
            'username' => $_SESSION['username'],
            'requests' => $requests
        ];
        
        $this->view('guru/verifikasi_peminjaman', $data);
    }
    
    public function prosesVerifikasi() {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $peminjamanModel = new Peminjaman_model();
            $barangModel = new Barang_model();

            $peminjaman_id = $_POST['peminjaman_id'];
            $status = $_POST['status']; // 'Disetujui' atau 'Ditolak'

            $peminjaman = $peminjamanModel->getPeminjamanById($peminjaman_id);

            if (!$peminjaman) {
                Flasher::setFlash('Gagal!', 'Data peminjaman tidak ditemukan.', 'danger');
                header('Location: ' . BASEURL . '/guru/verifikasi');
                exit;
            }

            if ($status === 'Ditolak') {
                $barangModel->tambahStok($peminjaman['barang_id'], 1);
            }
            
            if ($peminjamanModel->updateStatusPeminjaman($peminjaman_id, $status) > 0) {
                Flasher::setFlash('Berhasil!', 'Status peminjaman telah diperbarui.', 'success');
            } else {
                Flasher::setFlash('Gagal!', 'Gagal memperbarui status peminjaman.', 'danger');
            }
        }

        header('Location: ' . BASEURL . '/guru/verifikasi');
        exit;
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
}
