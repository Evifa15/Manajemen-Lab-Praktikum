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
        
        $keyword = $_GET['search'] ?? null;
        $role = $_GET['filter_role'] ?? null;

        $halaman = max(1, (int)$halaman);
        $limit = 5;
        $offset = ($halaman - 1) * $limit;

        $filters = [
            'keyword' => $keyword,
            'role' => $role
        ];

        $totalPengguna = $userModel->countAllUsers($filters);
        $totalHalaman = ceil($totalPengguna / $limit);
        
        $data = [
            'title' => 'Manajemen Pengguna',
            'users' => $userModel->getUsersPaginated($offset, $limit, $filters),
            'total_halaman' => $totalHalaman,
            'halaman_aktif' => $halaman,
            'total_pengguna' => $totalPengguna,
            'filters' => $filters
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
        
        $keyword = $_GET['search'] ?? null;
        $kondisi = $_GET['filter_kondisi'] ?? null;

        $halaman = max(1, (int)$halaman);
        $limit = 5;
        $offset = ($halaman - 1) * $limit;

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
            'filters' => $filters
        ];
        
        $this->view('admin/manajemen_barang', $data);
    }
    public function barang($halaman = 1) { $this->manajemenBarang($halaman); }
    public function tambahBarang() {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (empty($_POST['kondisi'])) {
                Flasher::setFlash('Gagal!', 'Kondisi barang harus diisi.', 'danger');
                header('Location: ' . BASEURL . '/admin/barang');
                exit;
            }

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
            } else {
                Flasher::setFlash('Gagal!', 'Terjadi kesalahan saat menambahkan barang.', 'danger');
            }
            header('Location: ' . BASEURL . '/admin/barang');
            exit;
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
            if (empty($_POST['kondisi'])) {
                Flasher::setFlash('Gagal!', 'Kondisi barang harus diisi.', 'danger');
                header('Location: ' . BASEURL . '/admin/barang');
                exit;
            }
            
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
    public function manajemenKelas($tab = 'guru', $halaman = 1) {
        $this->checkAuth();
        $kelasModel = new Kelas_model();
        $guruModel = new Guru_model();

        // PERBAIKAN: Ambil kedua keyword pencarian secara terpisah
        $keywordKelas = $_GET['search_kelas'] ?? null;
        $keywordGuru = $_GET['search_guru'] ?? null;

        $halaman = max(1, (int)$halaman);
        $limit = 5;
        
        $offsetKelas = ($tab === 'kelas') ? ($halaman - 1) * $limit : 0;
        $offsetGuru = ($tab === 'guru') ? ($halaman - 1) * $limit : 0;

        // Gunakan keyword yang sesuai untuk setiap query
        $totalKelas = $kelasModel->countAllKelas($keywordKelas);
        $totalHalamanKelas = ceil($totalKelas / $limit);
        $kelasData = $kelasModel->getKelasPaginated($offsetKelas, $limit, $keywordKelas);

        $totalGuru = $guruModel->countAllGuru($keywordGuru);
        $totalHalamanGuru = ceil($totalGuru / $limit);
        $guruData = $guruModel->getGuruPaginated($offsetGuru, $limit, $keywordGuru);

        $data = [
            'title' => 'Manajemen Kelas & Guru',
            'active_tab' => $tab,
            
            // PERBAIKAN: Kirim kedua keyword ke view
            'search_term_kelas' => $keywordKelas,
            'search_term_guru' => $keywordGuru,
            
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

        // PERUBAHAN: Panggil metode baru yang sudah mencakup pembuatan user
        if ($guruModel->createGuruAndUserAccount($_POST) > 0) {
            Flasher::setFlash('Berhasil!', 'Data guru beserta akun login berhasil dibuat.', 'success');
        } else {
            Flasher::setFlash('Gagal!', 'Gagal menambahkan guru. Pastikan NIP atau Nama Guru belum pernah terdaftar.', 'danger');
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
            
            if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

            if (!move_uploaded_file($file['tmp_name'], $targetDir . $namaFoto)) {
                Flasher::setFlash('Gagal!', 'Gagal mengunggah foto.', 'danger');
                header('Location: ' . BASEURL . '/admin/detailKelas/' . $_POST['kelas_id']);
                exit;
            }
        }
        
        // Gabungkan data POST dengan nama file foto
        $data = array_merge($_POST, ['foto' => $namaFoto]);

        // Panggil metode baru yang sudah mencakup pembuatan user dan profil siswa
        if ($siswaModel->createSiswaAndUserAccount($data) > 0) {
            Flasher::setFlash('Berhasil!', 'Data siswa beserta akun login berhasil dibuat.', 'success');
        } else {
            Flasher::setFlash('Gagal!', 'Gagal menambahkan siswa. Pastikan ID Siswa atau Nama Siswa belum pernah terdaftar.', 'danger');
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
    
    // --- METODE PROFIL & GANTI PASSWORD ---
    public function profile() {
        $this->checkAuth();
        $userModel = new User_model();
        $profileModel = new Profile_model();

        $data['user'] = $userModel->getUserById($_SESSION['user_id']);
        $data['profile'] = $profileModel->getProfileByRoleAndUserId($_SESSION['role'], $_SESSION['user_id']);
        $data['title'] = 'Profil Saya';
        
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

    // --- METODE IMPORT & HAPUS MASSAL ---
    public function importKelas() {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file_import'])) {
            $file = $_FILES['file_import'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                Flasher::setFlash('Gagal!', 'Terjadi error saat mengunggah file.', 'danger');
                header('Location: ' . BASEURL . '/admin/kelas');
                exit;
            }

            $fileType = pathinfo($file['name'], PATHINFO_EXTENSION);
            if ($fileType != 'csv') {
                Flasher::setFlash('Gagal!', 'Hanya file format .csv yang didukung saat ini.', 'danger');
                header('Location: ' . BASEURL . '/admin/kelas');
                exit;
            }

            $fileHandle = fopen($file['tmp_name'], 'r');
            fgetcsv($fileHandle); 

            $dataUntukImport = [];
            $errors = [];
            $baris = 1;

            $guruModel = new Guru_model();

            while (($row = fgetcsv($fileHandle)) !== FALSE) {
                $baris++;
                $namaKelas = trim($row[0] ?? '');
                $nipWali = trim($row[2] ?? '');

                if (empty($namaKelas) || empty($nipWali)) {
                    $errors[] = "Baris {$baris}: Nama Kelas atau NIP Wali Kelas kosong.";
                    continue;
                }

                $guru = $guruModel->getGuruByNip($nipWali);
                if (!$guru) {
                    $errors[] = "Baris {$baris}: Wali Kelas dengan NIP '{$nipWali}' tidak ditemukan.";
                    continue;
                }

                $dataUntukImport[] = [
                    'nama_kelas' => $namaKelas,
                    'wali_kelas_id' => $guru['id']
                ];
            }
            fclose($fileHandle);

            if (empty($errors) && !empty($dataUntukImport)) {
                $kelasModel = new Kelas_model();
                $hasil = $kelasModel->tambahKelasBatch($dataUntukImport);

                Flasher::setFlash('Berhasil!', "{$hasil['success']} data kelas berhasil diimpor.", 'success');
            } else {
                $pesanError = 'Proses import gagal. Detail: <br>' . implode('<br>', $errors);
                Flasher::setFlash('Gagal!', $pesanError, 'danger');
            }

        } else {
            Flasher::setFlash('Gagal!', 'Tidak ada file yang diunggah.', 'danger');
        }

        header('Location: ' . BASEURL . '/admin/kelas');
        exit;
    }

    public function importGuru() {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file_import_guru'])) {
            $file = $_FILES['file_import_guru'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                Flasher::setFlash('Gagal!', 'Terjadi error saat mengunggah file.', 'danger');
                header('Location: ' . BASEURL . '/admin/kelas/guru');
                exit;
            }

            $fileType = pathinfo($file['name'], PATHINFO_EXTENSION);
            if ($fileType != 'csv') {
                Flasher::setFlash('Gagal!', 'Hanya file format .csv yang didukung.', 'danger');
                header('Location: ' . BASEURL . '/admin/kelas/guru');
                exit;
            }

            $fileHandle = fopen($file['tmp_name'], 'r');
            fgetcsv($fileHandle); // Lewati header

            $dataUntukImport = [];
            while (($row = fgetcsv($fileHandle)) !== FALSE) {
                
                $jenisKelaminRaw = trim($row[2] ?? '');
                $normalizedJK = strtolower(str_replace(['-', ' '], '', $jenisKelaminRaw));

                $jenisKelaminFinal = '';
                if ($normalizedJK === 'lakilaki') {
                    $jenisKelaminFinal = 'Laki laki';
                } elseif ($normalizedJK === 'perempuan') {
                    $jenisKelaminFinal = 'Perempuan';
                }

                $dataUntukImport[] = [
                    'nama'          => trim($row[0] ?? ''),
                    'nip'           => trim($row[1] ?? ''),
                    'jenis_kelamin' => $jenisKelaminFinal,
                    'no_hp'         => trim($row[3] ?? ''),
                    'email'         => trim($row[4] ?? ''),
                    'alamat'        => trim($row[5] ?? '')
                ];
            }
            fclose($fileHandle);

            if (!empty($dataUntukImport)) {
                $guruModel = new Guru_model();
                $hasil = $guruModel->tambahGuruBatch($dataUntukImport);

                if ($hasil['failed'] > 0) {
                    $pesanError = "{$hasil['failed']} data guru gagal diimpor. Detail: <br>" . implode('<br>', $hasil['errors']);
                    Flasher::setFlash('Import Selesai dengan Error', $pesanError, 'danger');
                } else {
                    Flasher::setFlash('Berhasil!', "{$hasil['success']} data guru berhasil diimpor.", 'success');
                }

            } else {
                Flasher::setFlash('Gagal!', 'File CSV kosong atau formatnya salah.', 'danger');
            }
        } else {
            Flasher::setFlash('Gagal!', 'Tidak ada file yang diunggah.', 'danger');
        }

        header('Location: ' . BASEURL . '/admin/kelas/guru');
        exit;
    }
    
    public function hapusKelasMassal() {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['ids'])) {
            $ids = $_POST['ids'];
            $kelasModel = new Kelas_model();
            $rowCount = $kelasModel->hapusKelasMassal($ids);

            if ($rowCount > 0) {
                Flasher::setFlash('Berhasil!', "{$rowCount} data kelas berhasil dihapus.", 'success');
            } else {
                Flasher::setFlash('Gagal!', 'Tidak ada data kelas yang dihapus.', 'danger');
            }
        } else {
            Flasher::setFlash('Gagal!', 'Tidak ada data yang dipilih untuk dihapus.', 'danger');
        }
        header('Location: ' . BASEURL . '/admin/kelas/kelas');
        exit;
    }

    public function hapusGuruMassal() {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['ids'])) {
            $ids = $_POST['ids'];
            $guruModel = new Guru_model();
            $rowCount = $guruModel->hapusGuruMassal($ids);

            if ($rowCount > 0) {
                Flasher::setFlash('Berhasil!', "{$rowCount} data guru berhasil dihapus.", 'success');
            } else {
                Flasher::setFlash('Gagal!', 'Tidak ada data guru yang dihapus.', 'danger');
            }
        } else {
            Flasher::setFlash('Gagal!', 'Tidak ada data yang dipilih untuk dihapus.', 'danger');
        }
        header('Location: ' . BASEURL . '/admin/kelas/guru');
        exit;
    }
    
    public function view($view, $data = []) {
        extract($data);
        require_once '../app/views/layouts/admin_header.php';
        require_once '../app/views/' . $view . '.php';
        require_once '../app/views/layouts/admin_footer.php';
    }

    /**
     * ==========================================================
     * METODE BARU: Untuk menangani upload dan proses file import siswa.
     * ==========================================================
     */
    public function importSiswa() {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file_import_siswa']) && isset($_POST['kelas_id'])) {
            $kelas_id = $_POST['kelas_id'];
            $file = $_FILES['file_import_siswa'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                Flasher::setFlash('Gagal!', 'Terjadi error saat mengunggah file.', 'danger');
                header('Location: ' . BASEURL . '/admin/detailKelas/' . $kelas_id);
                exit;
            }

            $fileType = pathinfo($file['name'], PATHINFO_EXTENSION);
            if (strtolower($fileType) != 'csv') {
                Flasher::setFlash('Gagal!', 'Hanya file format .csv yang didukung.', 'danger');
                header('Location: ' . BASEURL . '/admin/detailKelas/' . $kelas_id);
                exit;
            }

            $fileHandle = fopen($file['tmp_name'], 'r');
            fgetcsv($fileHandle); // Lewati baris header

            $dataUntukImport = [];
            while (($row = fgetcsv($fileHandle)) !== FALSE) {
                if (!empty($row[0]) && !empty($row[1])) { // Pastikan nama dan ID siswa tidak kosong
                    $dataUntukImport[] = [
                        'nama'          => trim($row[0]),
                        'id_siswa'      => trim($row[1]),
                        'jenis_kelamin' => trim($row[2] ?? 'Laki laki'),
                    ];
                }
            }
            fclose($fileHandle);
            
            if (!empty($dataUntukImport)) {
                $siswaModel = new Siswa_model();
                $hasil = $siswaModel->importSiswaBatch($dataUntukImport, $kelas_id);

                if ($hasil['failed'] > 0) {
                    $pesan = "{$hasil['success']} data berhasil diimpor, {$hasil['failed']} data gagal. <br> Detail Error: <br>" . implode('<br>', $hasil['errors']);
                    Flasher::setFlash('Proses Selesai dengan Error', $pesan, 'danger');
                } else {
                    Flasher::setFlash('Berhasil!', "{$hasil['success']} data siswa berhasil diimpor.", 'success');
                }
            } else {
                Flasher::setFlash('Gagal!', 'File CSV kosong atau formatnya salah.', 'danger');
            }
        } else {
            Flasher::setFlash('Gagal!', 'Tidak ada file yang diunggah atau ID kelas tidak ditemukan.', 'danger');
        }

        header('Location: ' . BASEURL . '/admin/detailKelas/' . $_POST['kelas_id']);
        exit;
    }

    /**
     * ==========================================================
     * METODE BARU: Untuk menangani penghapusan banyak siswa.
     * ==========================================================
     */
    public function hapusSiswaMassal() {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['ids']) && isset($_POST['kelas_id'])) {
            $ids = $_POST['ids'];
            $kelas_id = $_POST['kelas_id'];
            $siswaModel = new Siswa_model();
            $rowCount = $siswaModel->hapusSiswaMassal($ids);

            if ($rowCount > 0) {
                Flasher::setFlash('Berhasil!', "{$rowCount} data siswa berhasil dihapus.", 'success');
            } else {
                Flasher::setFlash('Gagal!', 'Tidak ada data siswa yang dihapus.', 'danger');
            }
            header('Location: ' . BASEURL . '/admin/detailKelas/' . $kelas_id);
        } else {
            Flasher::setFlash('Gagal!', 'Tidak ada data yang dipilih atau ID kelas tidak ditemukan.', 'danger');
            // Redirect ke halaman kelas jika kelas_id tidak ada
            header('Location: ' . BASEURL . '/admin/kelas');
        }
        exit;
    }
}

