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
    
    // --- METODE MANAJEMEN PENGGUNA ---
     public function manajemenPengguna($halaman = 1) {
        $this->checkAuth();
        $userModel = new User_model();
        
        // 1. Tangkap input dari URL (jika ada)
        $keyword = $_GET['search'] ?? null;
        $role = $_GET['filter_role'] ?? null;

        $halaman = max(1, (int)$halaman);
        $limit = 5; // Tetap 5 baris per halaman
        $offset = ($halaman - 1) * $limit;

        // 2. Buat array filter untuk dikirim ke model
        $filters = [
            'keyword' => $keyword,
            'role' => $role
        ];

        // 3. Panggil model dengan filter
        $totalPengguna = $userModel->countAllUsers($filters);
        $totalHalaman = ceil($totalPengguna / $limit);
        
        $data = [
            'title' => 'Manajemen Pengguna',
            'users' => $userModel->getUsersPaginated($offset, $limit, $filters),
            'total_halaman' => $totalHalaman,
            'halaman_aktif' => $halaman,
            'total_pengguna' => $totalPengguna,
            'filters' => $filters // 4. Kirim filter kembali ke view
        ];
        
        $this->view('admin/manajemen_pengguna', $data);
    }
    
   public function tambahPengguna() {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userModel = new User_model();
            
            if (empty($_POST['username']) || empty($_POST['id_pengguna']) || empty($_POST['password']) || empty($_POST['role']) || empty($_POST['email'])) {
                Flasher::setFlash('Gagal!', 'Pastikan semua kolom terisi.', 'danger');
                header('Location: ' . BASEURL . '/admin/pengguna');
                exit;
            }
            
            if ($userModel->findUserByUsername($_POST['username'])) {
                Flasher::setFlash('Gagal!', 'Nama pengguna sudah ada.', 'danger');
                header('Location: ' . BASEURL . '/admin/pengguna');
                exit;
            }

            // Panggil fungsi baru yang sudah mendukung transaksi
            if ($userModel->createUserWithRole($_POST) > 0) {
                Flasher::setFlash('Berhasil!', 'Pengguna baru berhasil ditambahkan.', 'success');
            } else {
                Flasher::setFlash('Gagal!', 'Terjadi kesalahan saat menambahkan pengguna.', 'danger');
            }
            header('Location: ' . BASEURL . '/admin/pengguna');
            exit;
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
    
    public function getPenggunaById($id) {
        $this->checkAuth();
        $userModel = new User_model();
        $user = $userModel->getUserById($id);
        
        header('Content-Type: application/json');
        echo json_encode($user);
    }

    public function getPenggunaDetailById($id) {
        $this->checkAuth();
        $userModel = new User_model();
        $user = $userModel->getUserDetailById($id);
        
        header('Content-Type: application/json');
        echo json_encode($user);
    }
    
    public function ubahPengguna() {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userModel = new User_model();
            if ($userModel->updateUser($_POST) >= 0) {
                Flasher::setFlash('Berhasil!', 'Data pengguna berhasil diubah.', 'success');
            } else {
                Flasher::setFlash('Gagal!', 'Gagal mengubah data pengguna.', 'danger');
            }
            header('Location: ' . BASEURL . '/admin/pengguna');
            exit;
        }
    }

    // --- METODE MANAJEMEN BARANG ---
    public function manajemenBarang($halaman = 1) {
        $this->checkAuth();
        $barangModel = new Barang_model();
        
        // Tangkap input dari URL untuk search dan filter
        $keyword = $_GET['search'] ?? null;
        $kondisi = $_GET['filter_kondisi'] ?? null;

        $halaman = max(1, (int)$halaman);
        $limit = 5; // 3. Pagination diubah menjadi 5 baris
        $offset = ($halaman - 1) * $limit;

        // Buat array filter untuk dikirim ke model
        $filters = [
            'keyword' => $keyword,
            'kondisi' => $kondisi
        ];
        
        $totalBarang = $barangModel->countAllBarang($filters);
        $totalHalaman = ceil($totalBarang / $limit);
        
        $data = [
            'title' => 'Manajemen Barang',
            'items' => $barangModel->getBarangPaginated($offset, $limit, $filters),
            'total_halaman' => $totalHalaman,
            'halaman_aktif' => $halaman,
            'filters' => $filters // Kirim filter kembali ke view
        ];
        
        $this->view('admin/manajemen_barang', $data);
    }
    public function barang($halaman = 1) { $this->manajemenBarang($halaman); }
    public function tambahBarang() {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // --- TAMBAHAN KODE: Validasi sisi server untuk kondisi ---
            if (empty($_POST['kondisi'])) {
                Flasher::setFlash('Gagal!', 'Kondisi barang harus diisi.', 'danger');
                header('Location: ' . BASEURL . '/admin/barang');
                exit;
            }
            // --- AKHIR TAMBAHAN KODE ---

            $barangModel = new Barang_model();

            $namaGambar = null;
            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['gambar'];
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $namaGambar = uniqid() . '.' . $ext;
                $targetDir = APP_ROOT . '/public/img/barang/';
                
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                
                if (!move_uploaded_file($file['tmp_name'], $targetDir . $namaGambar)) {
                    Flasher::setFlash('Gagal!', 'Gagal mengunggah gambar.', 'danger');
                    header('Location: ' . BASEURL . '/admin/barang');
                    exit;
                }
            }
            
            $data = [
                'nama_barang' => $_POST['nama_barang'],
                'kode_barang' => $_POST['kode_barang'],
                'jumlah' => $_POST['jumlah'],
                'kondisi' => $_POST['kondisi'],
                'tanggal_pembelian' => $_POST['tanggal_pembelian'] ?? null,
                'lokasi_penyimpanan' => $_POST['lokasi_penyimpanan'] ?? null,
                'gambar' => $namaGambar
            ];
            
            if ($barangModel->tambahBarang($data) > 0) {
                Flasher::setFlash('Berhasil!', 'Barang baru berhasil ditambahkan.', 'success');
                header('Location: ' . BASEURL . '/admin/barang');
                exit;
            } else {
                Flasher::setFlash('Gagal!', 'Terjadi kesalahan saat menambahkan barang.', 'danger');
                header('Location: ' . BASEURL . '/admin/barang');
                exit;
            }
        }
    }
    public function getBarangById($id) {
        $this->checkAuth();
        $barangModel = new Barang_model();
        $item = $barangModel->getBarangById($id);
        
        header('Content-Type: application/json');
        echo json_encode($item);
    }
    public function ubahBarang() {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // --- TAMBAHAN KODE: Validasi sisi server untuk kondisi ---
            if (empty($_POST['kondisi'])) {
                Flasher::setFlash('Gagal!', 'Kondisi barang harus diisi.', 'danger');
                header('Location: ' . BASEURL . '/admin/barang');
                exit;
            }
            // --- AKHIR TAMBAHAN KODE ---
            
            $barangModel = new Barang_model();

            $namaGambar = $_POST['gambar_lama'];
            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['gambar'];
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $namaGambar = uniqid() . '.' . $ext;
                $targetDir = APP_ROOT . '/public/img/barang/';
                
                if (move_uploaded_file($file['tmp_name'], $targetDir . $namaGambar)) {
                    if ($_POST['gambar_lama'] != 'default.png' && file_exists($targetDir . $_POST['gambar_lama'])) {
                        unlink($targetDir . $_POST['gambar_lama']);
                    }
                } else {
                    $namaGambar = $_POST['gambar_lama'];
                }
            }

            $data = [
                'id' => $_POST['id'],
                'nama_barang' => $_POST['nama_barang'],
                'kode_barang' => $_POST['kode_barang'],
                'jumlah' => $_POST['jumlah'],
                'kondisi' => $_POST['kondisi'],
                'tanggal_pembelian' => $_POST['tanggal_pembelian'] ?? null,
                'lokasi_penyimpanan' => $_POST['lokasi_penyimpanan'] ?? null,
                'gambar' => $namaGambar
            ];

            if ($barangModel->ubahBarang($data) >= 0) {
                Flasher::setFlash('Berhasil!', 'Data barang berhasil diubah.', 'success');
            } else {
                Flasher::setFlash('Gagal!', 'Gagal mengubah data barang.', 'danger');
            }
            header('Location: ' . BASEURL . '/admin/barang');
            exit;
        }
    }
    public function hapusBarang($id) {
        $this->checkAuth();
        $barangModel = new Barang_model();
        
        if ($barangModel->hapusBarang($id) > 0) {
            Flasher::setFlash('Berhasil!', 'Barang berhasil dihapus.', 'success');
        } else {
            Flasher::setFlash('Gagal!', 'Barang gagal dihapus.', 'danger');
        }

        header('Location: ' . BASEURL . '/admin/barang');
        exit;
    }
    public function detailBarang($id) {
        $this->checkAuth();
        $barangModel = new Barang_model();
        $data = [
            'title' => 'Detail Barang',
            'item' => $barangModel->getBarangById($id)
        ];
        $this->view('admin/barang/detail', $data);
    }
    
    // --- METODE MANAJEMEN KELAS & GURU ---
    public function manajemenKelas($tab = 'kelas', $halaman = 1) {
        $this->checkAuth();
        $kelasModel = new Kelas_model();
        $guruModel = new Guru_model();

        $keyword = $_GET['search'] ?? null;
        $halaman = max(1, (int)$halaman);
        $limit = 5;
        
        $offsetKelas = ($tab === 'kelas') ? ($halaman - 1) * $limit : 0;
        $offsetGuru = ($tab === 'guru') ? ($halaman - 1) * $limit : 0;

        $totalKelas = $kelasModel->countAllKelas($keyword);
        $totalHalamanKelas = ceil($totalKelas / $limit);
        $kelasData = $kelasModel->getKelasPaginated($offsetKelas, $limit, $keyword);

        $totalGuru = $guruModel->countAllGuru($keyword);
        $totalHalamanGuru = ceil($totalGuru / $limit);
        $guruData = $guruModel->getGuruPaginated($offsetGuru, $limit, $keyword);

        $data = [
            'title' => 'Manajemen Kelas & Guru',
            'active_tab' => $tab,
            'search_term' => $keyword,
            
            'kelas' => $kelasData,
            'total_halaman_kelas' => $totalHalamanKelas,
            'halaman_aktif_kelas' => ($tab === 'kelas' ? $halaman : 1),

            'guru' => $guruData,
            'total_halaman_guru' => $totalHalamanGuru,
            'halaman_aktif_guru' => ($tab === 'guru' ? $halaman : 1),
            
            'all_guru' => $guruModel->getAllGuru()
        ];

        $this->view('admin/manajemen_kelas', $data);
    }
    public function tambahKelas() {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $kelasModel = new Kelas_model();
            if ($kelasModel->tambahKelas($_POST) > 0) {
                Flasher::setFlash('Berhasil!', 'Kelas baru berhasil ditambahkan.', 'success');
            } else {
                Flasher::setFlash('Gagal!', 'Terjadi kesalahan saat menambahkan kelas.', 'danger');
            }
        }
        header('Location: ' . BASEURL . '/admin/kelas/kelas');
        exit;
    }
    public function getKelasById($id) {
        $this->checkAuth();
        $kelasModel = new Kelas_model();
        header('Content-Type: application/json');
        echo json_encode($kelasModel->getKelasById($id));
    }
    public function ubahKelas() {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $kelasModel = new Kelas_model();
            if ($kelasModel->updateKelas($_POST) >= 0) {
                Flasher::setFlash('Berhasil!', 'Data kelas berhasil diubah.', 'success');
            } else {
                Flasher::setFlash('Gagal!', 'Gagal mengubah data kelas.', 'danger');
            }
        }
        header('Location: ' . BASEURL . '/admin/kelas/kelas');
        exit;
    }
    public function detailKelas($id, $halaman = 1) {
        $this->checkAuth();
        $kelasModel = new Kelas_model();
        $siswaModel = new Siswa_model();

        $keyword = $_GET['search'] ?? null;
        $halaman = max(1, (int)$halaman);
        $limit = 10;
        $offset = ($halaman - 1) * $limit;
        
        $totalSiswa = $siswaModel->countAllSiswaByKelasId($id, $keyword);
        $totalHalaman = ceil($totalSiswa / $limit);

        $data = [
            'title' => 'Detail Kelas',
            'kelas' => $kelasModel->getKelasById($id),
            'siswa' => $siswaModel->getSiswaByKelasIdPaginated($id, $offset, $limit, $keyword),
            'halaman_aktif' => $halaman,
            'total_halaman' => $totalHalaman,
            'search_term' => $keyword
        ];
        $this->view('admin/kelas/detail', $data);
    }
    public function hapusKelas($id) {
        $this->checkAuth();
        $kelasModel = new Kelas_model();
        if ($kelasModel->hapusKelas($id) > 0) {
            Flasher::setFlash('Berhasil!', 'Kelas berhasil dihapus.', 'success');
        } else {
            Flasher::setFlash('Gagal!', 'Kelas gagal dihapus.', 'danger');
        }
        header('Location: ' . BASEURL . '/admin/kelas/kelas');
        exit;
    }
    public function tambahGuru() {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $guruModel = new Guru_model();
            if ($guruModel->tambahGuru($_POST) > 0) {
                Flasher::setFlash('Berhasil!', 'Guru baru berhasil ditambahkan.', 'success');
            } else {
                Flasher::setFlash('Gagal!', 'Terjadi kesalahan saat menambahkan guru.', 'danger');
            }
        }
        header('Location: ' . BASEURL . '/admin/kelas/guru');
        exit;
    }
    public function getGuruById($id) {
        $this->checkAuth();
        $guruModel = new Guru_model();
        header('Content-Type: application/json');
        echo json_encode($guruModel->getGuruById($id));
    }
    public function detailGuru($id) {
        $this->checkAuth();
        $guruModel = new Guru_model();
        $data = [
            'title' => 'Detail Guru',
            'guru' => $guruModel->getGuruById($id)
        ];
        $this->view('admin/guru/detail', $data);
    }
    public function ubahGuru() {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $guruModel = new Guru_model();
            if ($guruModel->updateGuru($_POST) >= 0) {
                Flasher::setFlash('Berhasil!', 'Data guru berhasil diubah.', 'success');
            } else {
                Flasher::setFlash('Gagal!', 'Gagal mengubah data guru.', 'danger');
            }
        }
        header('Location: ' . BASEURL . '/admin/kelas/guru');
        exit;
    }
    public function hapusGuru($id) {
        $this->checkAuth();
        $guruModel = new Guru_model();
        if ($guruModel->hapusGuru($id) > 0) {
            Flasher::setFlash('Berhasil!', 'Guru berhasil dihapus.', 'success');
        } else {
            Flasher::setFlash('Gagal!', 'Guru gagal dihapus.', 'danger');
        }
        header('Location: ' . BASEURL . '/admin/kelas/guru');
        exit;
    }

    // --- METODE MANAJEMEN SISWA ---
    public function tambahSiswa() {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $siswaModel = new Siswa_model();
            
            $namaFoto = 'default.png';
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['foto'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $namaFoto = uniqid('siswa_') . '.' . $ext;
                $targetDir = APP_ROOT . '/public/img/siswa/';
                
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }

                if (!move_uploaded_file($file['tmp_name'], $targetDir . $namaFoto)) {
                    Flasher::setFlash('Gagal!', 'Gagal mengunggah foto.', 'danger');
                    header('Location: ' . BASEURL . '/admin/detailKelas/' . $_POST['kelas_id']);
                    exit;
                }
            }
            
            $data = array_merge($_POST, ['foto' => $namaFoto]);
            if ($siswaModel->tambahSiswa($data) > 0) {
                Flasher::setFlash('Berhasil!', 'Siswa baru berhasil ditambahkan.', 'success');
            } else {
                Flasher::setFlash('Gagal!', 'Gagal menambahkan siswa. Periksa kembali ID Siswa (tidak boleh sama).', 'danger');
            }
        }
        header('Location: ' . BASEURL . '/admin/detailKelas/' . $_POST['kelas_id']);
        exit;
    }
    public function getSiswaById($id) {
        $this->checkAuth();
        $siswaModel = new Siswa_model();
        header('Content-Type: application/json');
        echo json_encode($siswaModel->getSiswaById($id));
    }
    public function ubahSiswa() {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $siswaModel = new Siswa_model();
            
            $data = $_POST;
            
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                 $file = $_FILES['foto'];
                 $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                 $namaFotoBaru = uniqid('siswa_') . '.' . $ext;
                 $targetDir = APP_ROOT . '/public/img/siswa/';
                 move_uploaded_file($file['tmp_name'], $targetDir . $namaFotoBaru);
                 $data['foto'] = $namaFotoBaru;
            }

            if ($siswaModel->updateSiswa($data) >= 0) {
                Flasher::setFlash('Berhasil!', 'Data siswa berhasil diubah.', 'success');
            } else {
                 Flasher::setFlash('Gagal!', 'Gagal mengubah data siswa.', 'danger');
            }
        }
        header('Location: ' . BASEURL . '/admin/detailKelas/' . $_POST['kelas_id']);
        exit;
    }
    public function hapusSiswa($id, $kelas_id) {
        $this->checkAuth();
        $siswaModel = new Siswa_model();
        if ($siswaModel->hapusSiswa($id) > 0) {
            Flasher::setFlash('Berhasil!', 'Siswa berhasil dihapus dari kelas.', 'success');
        } else {
            Flasher::setFlash('Gagal!', 'Gagal menghapus siswa.', 'danger');
        }
        header('Location: ' . BASEURL . '/admin/detailKelas/' . $kelas_id);
        exit;
    }
    public function detailSiswa($id) {
        $this->checkAuth();
        $siswaModel = new Siswa_model();
        $data = [
            'title' => 'Detail Siswa',
            'siswa' => $siswaModel->getSiswaById($id)
        ];
        $this->view('admin/siswa/detail', $data);
    }
    
    // --- METODE MANAJEMEN LAPORAN ---
    public function laporanRiwayat($halaman = 1) {
        $this->checkAuth();
        $peminjamanModel = new Peminjaman_model();

        $halaman = max(1, (int)$halaman);
        $limit = 10;
        $offset = ($halaman - 1) * $limit;

        $filters = [
            'keyword' => $_GET['search'] ?? null,
            'start_date' => $_GET['start_date'] ?? null,
            'end_date' => $_GET['end_date'] ?? null
        ];

        $totalRiwayat = $peminjamanModel->countAllHistory($filters);
        $totalHalaman = ceil($totalRiwayat / $limit);
        
        $data = [
            'title' => 'Laporan & Riwayat',
            'history' => $peminjamanModel->getHistoryPaginated($offset, $limit, $filters),
            'total_halaman' => $totalHalaman,
            'halaman_aktif' => $halaman,
            'filters' => $filters
        ];

        $this->view('admin/laporan_riwayat', $data);
    }

    public function unduhLaporan() {
        $this->checkAuth();
        $peminjamanModel = new Peminjaman_model();

        $filters = [
            'keyword' => $_GET['search'] ?? null,
            'start_date' => $_GET['start_date'] ?? null,
            'end_date' => $_GET['end_date'] ?? null
        ];

        $dataToExport = $peminjamanModel->getAllHistoryForExport($filters);

        $filename = "laporan_peminjaman_" . date('Y-m-d') . ".csv";

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        fputcsv($output, [
            'No', 
            'Nama Peminjam', 
            'No. ID Peminjam', 
            'Nama Barang', 
            'Tanggal Pinjam', 
            'Tanggal Kembali', 
            'Status'
        ]);

        $no = 1;
        foreach ($dataToExport as $row) {
            $status_text = ($row['status'] == 'Dikembalikan') ? 'Tepat Waktu' : $row['status'];
            fputcsv($output, [
                $no++,
                $row['nama_peminjam'],
                $row['no_id_peminjam'],
                $row['nama_barang'],
                $row['tanggal_pinjam'],
                $row['tanggal_kembali'] ?? '-',
                $status_text
            ]);
        }

        fclose($output);
        exit();
    }
    
    // ✅ METODE PROFIL & GANTI PASSWORD (REVISI)
    public function profile() {
        $this->checkAuth();
        $userModel = new User_model();
        $profileModel = new Profile_model();

        $data['user'] = $userModel->getUserById($_SESSION['user_id']);
        $data['profile'] = $profileModel->getProfileByRoleAndUserId($_SESSION['role'], $_SESSION['user_id']);
        $data['title'] = 'Profil Saya';
        
        // Memanggil view secara langsung untuk menghindari masalah path
        extract($data);
        require_once '../app/views/layouts/admin_header.php';
        require_once '../app/views/shared/profile.php';
        require_once '../app/views/layouts/admin_footer.php';
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
        header('Location: ' . BASEURL . '/admin/profile');
        exit;
    }

    // --- AKHIR DARI METODE PROFIL ---

    public function view($view, $data = []) {
        extract($data);
        require_once '../app/views/layouts/admin_header.php';
        require_once '../app/views/' . $view . '.php';
        require_once '../app/views/layouts/admin_footer.php';
    }
}