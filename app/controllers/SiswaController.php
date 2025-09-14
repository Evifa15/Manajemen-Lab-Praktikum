<?php

class SiswaController {

    /**
     * Memeriksa apakah pengguna sudah login dan memiliki peran sebagai 'siswa'.
     * Jika tidak, akan diarahkan ke halaman login.
     */
    private function checkAuth() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'siswa') {
            Flasher::setFlash('Akses ditolak!', 'Anda harus login sebagai siswa.', 'danger');
            header('Location: '. BASEURL . '/login?role=siswa');
            exit;
        }
    }

    /**
     * Menampilkan halaman dashboard utama untuk siswa.
     */
    public function index() {
        $this->checkAuth();
        $data = [
            'title' => 'Dashboard Siswa',
            'username' => $_SESSION['username']
        ];
        $this->view('siswa/index', $data);
    }

    /**
     * Menampilkan halaman katalog barang dengan data dinamis dan pagination.
     * @param int $halaman Nomor halaman saat ini.
     */
    public function katalogBarang($halaman = 1) {
        $this->checkAuth();
        $barangModel = new Barang_model();
        $siswaModel = new Siswa_model(); // Tambahkan model siswa
        
        $halaman = max(1, (int)$halaman);
        $limit = 15;
        $offset = ($halaman - 1) * $limit;
        
        $filters = [
            'keyword' => $_GET['search'] ?? null,
            'status' => $_GET['filter_ketersediaan'] ?? null // Ubah nama variabel menjadi 'status'
        ];
        
        $items = $barangModel->getBarangPaginated($offset, $limit, $filters);
        $totalBarang = $barangModel->countAllBarang($filters);
        $totalHalaman = ceil($totalBarang / $limit);

        // Ambil data detail barang yang ada di keranjang
        $keranjang_ids = $_SESSION['keranjang'] ?? [];
        $data_keranjang = $barangModel->getBarangByIds($keranjang_ids);
        
        // Ambil data siswa yang sedang login
        $data_siswa = $siswaModel->getSiswaByUserId($_SESSION['user_id']);

        $data = [
            'title' => 'Katalog Barang',
            'username' => $_SESSION['username'],
            'items' => $items,
            'total_halaman' => $totalHalaman,
            'halaman_aktif' => $halaman,
            'filters' => $filters,
            'jumlah_keranjang' => count($keranjang_ids),
            'data_keranjang' => $data_keranjang,
            'data_siswa' => $data_siswa // Kirim data siswa ke view
        ];
        
        $this->view('siswa/katalog_barang', $data);
    }

    /**
     * Memproses penambahan barang ke keranjang (session).
     */
    public function tambahKeKeranjang() {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['barang_id'])) {
            $barangModel = new Barang_model();
            $barang_id = $_POST['barang_id'];

            if (!isset($_SESSION['keranjang'])) {
                $_SESSION['keranjang'] = [];
            }

            $barang = $barangModel->getBarangById($barang_id);

            // ✅ PERBAIKAN DI SINI:
            // Tambahkan pengecekan tambahan untuk memastikan stok > 0 sebelum menambahkan ke keranjang
            if ($barang && $barang['jumlah'] > 0) {
                if (!in_array($barang_id, $_SESSION['keranjang'])) {
                    $_SESSION['keranjang'][] = $barang_id;
                    Flasher::setFlash('Berhasil!', 'Barang ditambahkan ke keranjang.', 'success');
                } else {
                    Flasher::setFlash('Info!', 'Barang sudah ada di keranjang.', 'danger');
                }
            } else {
                Flasher::setFlash('Gagal!', 'Stok barang sudah habis atau tidak tersedia.', 'danger');
            }
        }
        header('Location: ' . BASEURL . '/siswa/katalog');
        exit;
    }
    /**
     * Menghapus item dari keranjang (session).
     */
    public function hapusDariKeranjang($id) {
        $this->checkAuth();
        if (isset($_SESSION['keranjang'])) {
            $key = array_search($id, $_SESSION['keranjang']);
            if ($key !== false) {
                unset($_SESSION['keranjang'][$key]);
                Flasher::setFlash('Berhasil!', 'Item dihapus dari keranjang.', 'success');
            }
        }
        header('Location: ' . BASEURL . '/siswa/katalog');
        exit;
    }

    /**
     * Memproses semua item di keranjang untuk diajukan sebagai peminjaman.
     */
    public function prosesPeminjaman() {
    $this->checkAuth();

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['barang_id'])) {
        $siswaModel = new Siswa_model();
        $kelasModel = new Kelas_model();
        $peminjamanModel = new Peminjaman_model();
        $barangModel = new Barang_model(); 

        $siswa = $siswaModel->getSiswaByUserId($_SESSION['user_id']);
        if (!$siswa) {
            Flasher::setFlash('Gagal!', 'Data siswa tidak ditemukan.', 'danger');
            header('Location: ' . BASEURL . '/siswa/katalog');
            exit;
        }

        $kelas = $kelasModel->getKelasById($siswa['kelas_id']);
        $waliKelasId = $kelas['wali_kelas_id'] ?? null;

        if (is_null($waliKelasId)) {
            Flasher::setFlash('Gagal!', 'Kelas Anda belum memiliki wali kelas. Hubungi Admin.', 'danger');
            header('Location: ' . BASEURL . '/siswa/katalog');
            exit;
        }

        $dataUntukDisimpan = [];
        $jumlah_pinjam_form = $_POST['jumlah_pinjam'];

        foreach ($_POST['barang_id'] as $barang_id) {
            $jumlah = isset($jumlah_pinjam_form[$barang_id]) ? (int)$jumlah_pinjam_form[$barang_id] : 1;

            $barang = $barangModel->getBarangById($barang_id);
            if (!$barang || $jumlah <= 0 || $jumlah > $barang['jumlah']) {
                Flasher::setFlash('Gagal!', 'Jumlah peminjaman untuk ' . htmlspecialchars($barang['nama_barang']) . ' tidak valid atau melebihi stok yang tersedia.', 'danger');
                header('Location: ' . BASEURL . '/siswa/katalog');
                exit;
            }

            $dataUntukDisimpan[] = [
                'user_id' => $_SESSION['user_id'],
                'barang_id' => $barang_id,
                'jumlah_pinjam' => $jumlah,
                'tanggal_pinjam' => $_POST['tanggal_pinjam'],
                'tanggal_kembali_diajukan' => $_POST['tanggal_kembali'],
                'keperluan' => $_POST['keperluan'],
                'verifikator_id' => $waliKelasId
            ];
        }

        $hasil = $peminjamanModel->createPeminjamanBatch($dataUntukDisimpan);

        if ($hasil > 0) {
            unset($_SESSION['keranjang']);
            Flasher::setFlash('Berhasil!', 'Pengajuan Anda telah dikirim ke wali kelas untuk verifikasi.', 'success');
        } else {
            Flasher::setFlash('Gagal!', 'Terjadi kesalahan saat memproses pengajuan.', 'danger');
        }
    } else {
         Flasher::setFlash('Gagal!', 'Keranjang kosong atau permintaan tidak valid.', 'danger');
    }

    header('Location: ' . BASEURL . '/siswa/katalog');
    exit;
}
    /** 
    * Menampilkan halaman pengembalian barang.
    */
   public function pengembalianBarang($peminjamanId = null) {
    $this->checkAuth();
    $peminjamanModel = new Peminjaman_model();
    $barangModel = new Barang_model();

    $data_peminjaman = $peminjamanModel->getPeminjamanByIdAndUserId($peminjamanId, $_SESSION['user_id']); // ✅ Perbaikan di sini

    if (!$data_peminjaman || $data_peminjaman['status'] !== 'Disetujui') {
        Flasher::setFlash('Gagal!', 'Peminjaman tidak valid atau belum disetujui.', 'danger');
        header('Location: ' . BASEURL . '/siswa/riwayat');
        exit;
    }

    $data = [
        'title' => 'Form Pengembalian',
        'username' => $_SESSION['username'],
        'peminjaman' => $data_peminjaman,
        'barang' => $barangModel->getBarangById($data_peminjaman['barang_id'])
    ];

    $this->view('siswa/pengembalian_barang', $data);
}
    /**
     * Menampilkan riwayat peminjaman barang untuk siswa yang sedang login.
     */
   public function riwayatPeminjaman($halaman = 1) {
    $this->checkAuth();
    $peminjamanModel = new Peminjaman_model();

    $halaman = max(1, (int)$halaman);
    $limit = 10;
    $offset = ($halaman - 1) * $limit;
    $userId = $_SESSION['user_id'];
    $keyword = $_GET['search'] ?? null;

    $totalRiwayat = $peminjamanModel->countHistoryByUserId($userId, $keyword);
    $totalHalaman = ceil($totalRiwayat / $limit);

    $data = [
        'title' => 'Riwayat Peminjaman',
        'username' => $_SESSION['username'],
        'history' => $peminjamanModel->getHistoryByUserId($userId, $offset, $limit, $keyword),
        'total_halaman' => $totalHalaman,
        'halaman_aktif' => $halaman,
        'keyword' => $keyword
    ];

    $this->view('siswa/riwayat_peminjaman', $data);
}
    
    /**
     * Menampilkan halaman profil siswa.
     */
    public function profile() {
        $this->checkAuth();
        $userModel = new User_model();
        $profileModel = new Profile_model();

        $data['user'] = $userModel->getUserById($_SESSION['user_id']);
        $data['profile'] = $profileModel->getProfileByRoleAndUserId($_SESSION['role'], $_SESSION['user_id']);
        $data['title'] = 'Profil Saya';
        
        extract($data);
        require_once '../app/views/layouts/siswa_header.php';
        require_once '../app/views/admin/profile.php';
        require_once '../app/views/layouts/siswa_footer.php';
    }

    /**
     * Memproses permintaan perubahan kata sandi.
     */
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
        header('Location: ' . BASEURL . '/siswa/profile');
        exit;
    }

    /**
     * Helper function untuk memuat file view beserta header dan footer.
     */
    public function view($view, $data = []) {
        extract($data);
        require_once '../app/views/layouts/siswa_header.php';
        require_once '../app/views/' . $view . '.php';
        require_once '../app/views/layouts/siswa_footer.php';
    }
    public function prosesPengembalian() {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['peminjaman_id']) && isset($_FILES['bukti_kembali'])) {
            $peminjamanModel = new Peminjaman_model();
            $barangModel = new Barang_model();
            
            // Logika upload foto bukti pengembalian
            $namaFoto = null;
            if ($_FILES['bukti_kembali']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['bukti_kembali'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $namaFotoBaru = 'bukti_' . uniqid() . '.' . $ext;
                $targetDir = APP_ROOT . '/public/img/bukti_kembali/';
                if (move_uploaded_file($file['tmp_name'], $targetDir . $namaFotoBaru)) {
                    $namaFoto = $namaFotoBaru;
                }
            }
            
            // Ambil data peminjaman untuk perbandingan tanggal
            $peminjaman = $peminjamanModel->getPeminjamanById($_POST['peminjaman_id']);
            $tanggal_wajib_kembali = strtotime($peminjaman['tanggal_wajib_kembali']);
            $tanggal_sekarang = strtotime(date('Y-m-d'));
            
            // Tentukan status pengembalian (Tepat Waktu atau Terlambat)
            $status_pengembalian = ($tanggal_sekarang <= $tanggal_wajib_kembali) ? 'Tepat Waktu' : 'Terlambat';
            
            $data = [
                'id' => $_POST['peminjaman_id'],
                'barang_id' => $peminjaman['barang_id'],
                'jumlah_pinjam' => $peminjaman['jumlah_pinjam'],
                'tanggal_kembali' => date('Y-m-d'),
                'bukti_kembali' => $namaFoto,
                'status_pengembalian' => $status_pengembalian,
                'status' => 'Selesai'
            ];
            
            // Panggil model untuk mengupdate database
            if ($peminjamanModel->updatePengembalian($data) > 0) {
                // Jika berhasil, panggil model barang untuk menambah stok
                $barangModel->tambahStok($data['barang_id'], $data['jumlah_pinjam']);
                Flasher::setFlash('Berhasil!', 'Pengembalian barang berhasil dicatat.', 'success');
            } else {
                Flasher::setFlash('Gagal!', 'Terjadi kesalahan saat memproses pengembalian.', 'danger');
            }
        } else {
            Flasher::setFlash('Gagal!', 'Permintaan tidak valid.', 'danger');
        }
        header('Location: ' . BASEURL . '/siswa/riwayat');
        exit;
    }
public function getPeminjamanById($id) {
    header('Content-Type: application/json');
    $peminjamanModel = new Peminjaman_model();
    $barangModel = new Barang_model();

    $peminjaman = $peminjamanModel->getPeminjamanByIdAndUserId($id, $_SESSION['user_id']);

    if ($peminjaman) {
        $barang = $barangModel->getBarangById($peminjaman['barang_id']);
        $peminjaman['nama_barang'] = $barang['nama_barang'];
        $peminjaman['kode_barang'] = $barang['kode_barang'];
        echo json_encode($peminjaman);
    } else {
        echo json_encode(null);
    }
    exit;
}
}
