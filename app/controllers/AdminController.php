<?php

class AdminController {

    private function checkAuth() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
            Flasher::setFlash('Akses ditolak!', 'Anda harus login sebagai admin.', 'danger');
            header('Location: ' . BASEURL . '/login?role=admin');
            exit;
        }
    }

    public function index() {
        $this->checkAuth();
        $data = [
            'title' => 'Dashboard Admin',
            'username' => $_SESSION['username']
        ];
        $this->view('admin/index', $data);
    }
    
    public function manajemenPengguna() {
        $this->checkAuth();
        $userModel = new User_model();
        $data = [
            'title' => 'Manajemen Pengguna',
            'users' => $userModel->getAllUsers()
        ];
        $this->view('admin/manajemen_pengguna', $data);
    }
    
    public function tambahPengguna() {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userModel = new User_model();
            
            if (empty($_POST['username']) || empty($_POST['id_pengguna']) || empty($_POST['password']) || empty($_POST['email']) || empty($_POST['role'])) {
                Flasher::setFlash('Gagal menambah pengguna!', 'Pastikan semua kolom terisi.', 'danger');
                header('Location: ' . BASEURL . '/admin/pengguna');
                exit;
            }
            
            if ($userModel->findUserByUsername($_POST['username'])) {
                Flasher::setFlash('Gagal menambah pengguna!', 'Nama pengguna sudah ada.', 'danger');
                header('Location: ' . BASEURL . '/admin/pengguna');
                exit;
            }

            if ($userModel->tambahUser($_POST) > 0) {
                Flasher::setFlash('Berhasil!', 'Pengguna baru berhasil ditambahkan.', 'success');
                header('Location: ' . BASEURL . '/admin/pengguna');
                exit;
            } else {
                Flasher::setFlash('Gagal!', 'Terjadi kesalahan saat menambahkan pengguna.', 'danger');
                header('Location: ' . BASEURL . '/admin/pengguna');
                exit;
            }
        }
    }

    public function hapusPengguna($id) {
        $this->checkAuth();
        
        $userModel = new User_model();
        if ($userModel->hapusUser($id) > 0) {
            Flasher::setFlash('Berhasil!', 'Pengguna berhasil dihapus.', 'success');
        } else {
            Flasher::setFlash('Gagal!', 'Pengguna gagal dihapus.', 'danger');
        }

        header('Location: ' . BASEURL . '/admin/pengguna');
        exit;
    }
    
    // --- Metode BARU: Mengambil data pengguna untuk diedit (via AJAX) ---
    public function getPenggunaById($id) {
        $this->checkAuth();
        $userModel = new User_model();
        $user = $userModel->getUserById($id);
        
        header('Content-Type: application/json');
        echo json_encode($user);
    }
    
    // --- Metode BARU: Memproses form ubah pengguna ---
    public function ubahPengguna() {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userModel = new User_model();
            if ($userModel->updateUser($_POST) > 0) {
                Flasher::setFlash('Berhasil!', 'Data pengguna berhasil diubah.', 'success');
            } else {
                Flasher::setFlash('Gagal!', 'Tidak ada data yang diubah atau terjadi kesalahan.', 'danger');
            }
            header('Location: ' . BASEURL . '/admin/pengguna');
            exit;
        }
    }
    // --- Akhir metode BARU ---

    public function manajemenBarang() {
        $this->checkAuth();
        $data = [
            'title' => 'Manajemen Barang',
            'username' => $_SESSION['username']
        ];
        $this->view('admin/manajemen_barang', $data);
    }

    public function manajemenKelas() {
        $this->checkAuth();
        $data = [
            'title' => 'Manajemen Kelas',
            'username' => $_SESSION['username']
        ];
        $this->view('admin/manajemen_kelas', $data);
    }

    public function laporanRiwayat() {
        $this->checkAuth();
        $data = [
            'title' => 'Laporan & Riwayat',
            'username' => $_SESSION['username']
        ];
        $this->view('admin/laporan_riwayat', $data);
    }

    public function profile() {
        $this->checkAuth();
        $data = [
            'title' => 'Profil Saya',
            'username' => $_SESSION['username'],
            'email' => $_SESSION['email'] ?? 'admin@example.com',
            'role' => $_SESSION['role']
        ];
        
        extract($data);
        require_once '../app/views/layouts/header.php';
        require_once '../app/views/admin/profile.php';
        require_once '../app/views/layouts/footer.php';
    }

    public function view($view, $data = []) {
        extract($data);
        require_once '../app/views/layouts/admin_header.php';
        require_once '../app/views/' . $view . '.php';
        require_once '../app/views/layouts/admin_footer.php';
    }
}