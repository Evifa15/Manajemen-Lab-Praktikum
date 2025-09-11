<?php

class AdminController {

    /**
     * =================================================================
     * FUNGSI UNTUK OTENTIKASI (Saat ini dikosongkan)
     * =================================================================
     * Method ini akan memeriksa apakah user sudah login sebagai admin.
     */
    private function checkAuth() {
        // Logika otentikasi akan diaktifkan kembali nanti
    }

    /**
     * =================================================================
     * HALAMAN UTAMA (DASHBOARD)
     * =================================================================
     */
    public function index() {
        $data = [ 'title' => 'Dashboard Admin' ];
        $this->view('admin/index', $data);
    }
    
     /**
     * =================================================================
     * MANAJEMEN PENGGUNA (STAFF, GURU, SISWA, AKUN)
     * =================================================================
     */
    public function manajemenPengguna($tab = 'staff', $halaman = 1) {
        if (empty($tab)) { $tab = 'staff'; }
        
        $halaman = max(1, (int)$halaman);
        $limit = 10;
        $offset = ($halaman - 1) * $limit;

        $data = [
            'title' => 'Manajemen Pengguna',
            'active_tab' => $tab,
            'halaman_aktif' => $halaman,
            'limit' => $limit
        ];

        // Memuat model yang diperlukan
        $staffModel = new Staff_model();
        $guruModel = new Guru_model(); 
        $siswaModel = new Siswa_model();
        $userModel = new User_model();

        // Mengambil data berdasarkan tab yang aktif
        switch ($tab) {
            case 'staff':
                $keyword = $_GET['search_staff'] ?? null;
                $data['staff'] = $staffModel->getStaffPaginated($offset, $limit, $keyword);
                $totalStaff = $staffModel->countAllStaff($keyword);
                $data['total_halaman'] = ceil($totalStaff / $limit);
                $data['search_term_staff'] = $keyword;
                break;

            case 'guru':
                $keyword = $_GET['search_guru'] ?? null;
                $data['guru'] = $guruModel->getGuruPaginated($offset, $limit, $keyword);
                $totalGuru = $guruModel->countAllGuru($keyword);
                $data['total_halaman'] = ceil($totalGuru / $limit);
                $data['search_term_guru'] = $keyword;
                break;
            case 'siswa':
                $keyword = $_GET['search_siswa'] ?? null;
                $data['siswa'] = $siswaModel->getAllSiswaPaginated($offset, $limit, $keyword);
                $totalSiswa = $siswaModel->countAllSiswa($keyword);
                $data['total_halaman'] = ceil($totalSiswa / $limit);
                $data['search_term_siswa'] = $keyword;
                break;
            case 'akun':
                $filters = [
                    'keyword' => $_GET['search'] ?? null,
                    'role'    => $_GET['filter_role'] ?? null
                ];
                $data['users'] = $userModel->getUsersPaginated($offset, $limit, $filters);
                $totalAkun = $userModel->countAllUsers($filters);
                $data['total_halaman'] = ceil($totalAkun / $limit);
                $data['filters'] = $filters;
                break;
        }
        
        $this->view('admin/manajemen_pengguna', $data);
    }
    
    /**
     * =================================================================
     * HALAMAN MANAJEMEN BARANG
     * =================================================================
     */
    public function manajemenBarang($halaman = 1) {
        $data = [ 'title' => 'Manajemen Barang', 'filters' => [] ];
        $this->view('admin/manajemen_barang', $data);
    }
    
    /**
     * =================================================================
     * HALAMAN MANAJEMEN KELAS
     * =================================================================
     */
    public function manajemenKelas($halaman = 1) {
        $halaman = max(1, (int)$halaman);
        $limit = 10;
        $offset = ($halaman - 1) * $limit;
        $keyword = $_GET['search_kelas'] ?? null;

        $kelasModel = new Kelas_model();
        $guruModel = new Guru_model();

        $data['title'] = 'Manajemen Kelas';
        $data['all_guru'] = $guruModel->getAllGuru();
        $data['kelas'] = $kelasModel->getKelasPaginated($offset, $limit, $keyword);
        $data['total_kelas'] = $kelasModel->countAllKelas($keyword);
        $data['total_halaman'] = ceil($data['total_kelas'] / $limit);
        $data['halaman_aktif'] = $halaman;
        $data['search_term_kelas'] = $keyword;
        
        $this->view('admin/manajemen_kelas', $data);
    }

    /**
     * =================================================================
     * PROSES TAMBAH KELAS
     * =================================================================
     */
    public function tambahKelas() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $kelasModel = new Kelas_model();

            if (empty($_POST['wali_kelas_id'])) {
                 Flasher::setFlash('Gagal!', 'Wali kelas tidak boleh kosong.', 'danger');
            } elseif ($kelasModel->tambahKelas($_POST) > 0) {
                Flasher::setFlash('Berhasil!', 'Data kelas berhasil ditambahkan.', 'success');
            } else {
                Flasher::setFlash('Gagal!', 'Gagal menambahkan kelas.', 'danger');
            }
        }
        header('Location: ' . BASEURL . '/admin/kelas');
        exit;
    }

    /**
     * =================================================================
     * PROSES IMPORT KELAS DARI FILE CSV
     * =================================================================
     */
    public function importKelas() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file_import_kelas'])) {
            $file = $_FILES['file_import_kelas'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                Flasher::setFlash('Gagal!', 'Terjadi error saat mengunggah file.', 'danger');
            } elseif (strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) != 'csv') {
                Flasher::setFlash('Gagal!', 'Hanya file format .csv yang didukung.', 'danger');
            } else {
                $fileHandle = fopen($file['tmp_name'], 'r');
                fgetcsv($fileHandle); // Lewati header

                $guruModel = new Guru_model();
                $allGuru = $guruModel->getAllGuru();
                $guruMap = array_column($allGuru, 'id', 'nip');

                $dataUntukImport = [];
                $errors = [];
                $line = 2;
                while (($row = fgetcsv($fileHandle)) !== FALSE) {
                    $nama_kelas = trim($row[0]);
                    $nip_wali = trim($row[1]);

                    if (empty($nama_kelas)) {
                        $errors[] = "Baris {$line}: Nama kelas kosong.";
                    } elseif (empty($nip_wali) || !isset($guruMap[$nip_wali])) {
                        $errors[] = "Baris {$line}: NIP Wali Kelas '{$nip_wali}' tidak ditemukan.";
                    } else {
                        $dataUntukImport[] = [
                            'nama_kelas' => $nama_kelas,
                            'wali_kelas_id' => $guruMap[$nip_wali]
                        ];
                    }
                    $line++;
                }
                fclose($fileHandle);

                if (empty($errors)) {
                    $kelasModel = new Kelas_model();
                    $hasil = $kelasModel->tambahKelasBatch($dataUntukImport);
                    
                    if ($hasil['failed'] > 0) {
                         Flasher::setFlash('Gagal!', "{$hasil['failed']} data kelas gagal diimpor.", 'danger');
                    } else {
                         Flasher::setFlash('Berhasil!', "{$hasil['success']} data kelas berhasil diimpor.", 'success');
                    }

                } else {
                    $pesan = "Gagal mengimpor data kelas. <br> Detail: <br>" . implode('<br>', $errors);
                    Flasher::setFlash('Proses Gagal', $pesan, 'danger');
                }
            }
        } else {
            Flasher::setFlash('Gagal!', 'Tidak ada file yang diunggah.', 'danger');
        }

        header('Location: ' . BASEURL . '/admin/kelas');
        exit;
    }
    
    /**
     * =================================================================
     * PROSES HAPUS KELAS
     * =================================================================
     */
    public function hapusKelas($id) {
        $kelasModel = new Kelas_model();
        $result = $kelasModel->hapusKelas($id);

        if ($result > 0) {
            Flasher::setFlash('Berhasil!', 'Data kelas berhasil dihapus.', 'success');
        } else {
            Flasher::setFlash('Gagal!', 'Gagal menghapus kelas. Pastikan tidak ada siswa yang terdaftar di kelas ini.', 'danger');
        }
        header('Location: ' . BASEURL . '/admin/kelas');
        exit;
    }
    
    /**
     * =================================================================
     * HALAMAN LAPORAN & RIWAYAT
     * =================================================================
     */
    public function laporanRiwayat($halaman = 1) {
        $data = [ 'title' => 'Laporan & Riwayat', 'filters' => [] ];
        $this->view('admin/laporan_riwayat', $data);
    }
    
    /**
     * =================================================================
     * PROSES TAMBAH STAFF BARU
     * =================================================================
     */
    public function tambahStaff() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $staffModel = new Staff_model();
            
            if ($staffModel->createStaffAndUserAccount($_POST) > 0) {
                Flasher::setFlash('Berhasil!', 'Data staff berhasil ditambahkan.', 'success');
            } else {
                Flasher::setFlash('Gagal!', 'Gagal menambahkan staff. Pastikan ID Staff unik.', 'danger');
            }
        }
        header('Location: ' . BASEURL . '/admin/pengguna/staff');
        exit;
    }

       
    /**
     * =================================================================
     * PROSES HAPUS STAFF
     * =================================================================
     */
    public function hapusStaff($id) {
        $staffModel = new Staff_model();
        if ($staffModel->hapusStaff($id) > 0) {
            Flasher::setFlash('Berhasil!', 'Data staff berhasil dihapus.', 'success');
        } else {
            Flasher::setFlash('Gagal!', 'Gagal menghapus data staff.', 'danger');
        }
        header('Location: ' . BASEURL . '/admin/pengguna/staff');
        exit;
    }

    
    /**
     * =================================================================
     * FUNGSI VIEW LOADER
     * =================================================================
     */
    public function view($view, $data = []) {
        extract($data);
        require_once '../app/views/layouts/admin_header.php';
        require_once '../app/views/' . $view . '.php';
        require_once '../app/views/layouts/admin_footer.php';
    }


    /**
     * =================================================================
     * PROSES IMPORT STAFF DARI FILE CSV
     * =================================================================
     */
    public function importStaff() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file_import_staff'])) {
            $file = $_FILES['file_import_staff'];

            // Validasi file
            if ($file['error'] !== UPLOAD_ERR_OK) {
                Flasher::setFlash('Gagal!', 'Terjadi error saat mengunggah file.', 'danger');
            } elseif (strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) != 'csv') {
                Flasher::setFlash('Gagal!', 'Hanya file format .csv yang didukung.', 'danger');
            } else {
                // Proses file CSV
                $fileHandle = fopen($file['tmp_name'], 'r');
                fgetcsv($fileHandle); // Lewati baris header

                $dataUntukImport = [];
                while (($row = fgetcsv($fileHandle)) !== FALSE) {
                    if (!empty($row[0]) && !empty($row[1])) { // Pastikan nama dan ID tidak kosong
                        $dataUntukImport[] = [
                            'nama'          => trim($row[0]),
                            'id_staff'      => trim($row[1]),
                            'jenis_kelamin' => trim($row[2] ?? 'Laki-laki'),
                            'no_hp'         => trim($row[3] ?? null),
                            'email'         => trim($row[4] ?? null),
                            'ttl'           => null,
                            'agama'         => null,
                            'alamat'        => null
                        ];
                    }
                }
                fclose($fileHandle);
                
                if (!empty($dataUntukImport)) {
                    $staffModel = new Staff_model();
                    $hasil = $staffModel->importStaffBatch($dataUntukImport);

                    if ($hasil['failed'] > 0) {
                        $pesan = "{$hasil['success']} data berhasil diimpor, {$hasil['failed']} data gagal. <br> Detail Error: <br>" . implode('<br>', $hasil['errors']);
                        Flasher::setFlash('Proses Selesai dengan Error', $pesan, 'danger');
                    } else {
                        Flasher::setFlash('Berhasil!', "{$hasil['success']} data staff berhasil diimpor.", 'success');
                    }
                } else {
                    Flasher::setFlash('Gagal!', 'File CSV kosong atau formatnya salah.', 'danger');
                }
            }
        } else {
            Flasher::setFlash('Gagal!', 'Tidak ada file yang diunggah.', 'danger');
        }

        header('Location: ' . BASEURL . '/admin/pengguna/staff');
        exit;
    }

    /**
 * =================================================================
 * ENDPOINT AJAX UNTUK PENCARIAN STAFF
 * =================================================================
 * Mengembalikan data staff dalam format JSON tanpa me-render view.
 */
public function searchStaff() {
    header('Content-Type: application/json');
    $keyword = $_GET['keyword'] ?? '';

    $staffModel = new Staff_model();
    // Kita gunakan method yang sudah ada, tapi tanpa paginasi untuk simplicity
    $staffData = $staffModel->getStaffPaginated(0, 999, $keyword); 

    echo json_encode($staffData);
    exit(); // Penting untuk menghentikan eksekusi agar tidak ada HTML lain yang tercetak
}

/**
     * =================================================================
     * ENDPOINT AJAX UNTUK MENGAMBIL DATA STAFF BY ID
     * =================================================================
     */
    public function getStaffById($id) {
        header('Content-Type: application/json');
        $staffModel = new Staff_model();
        $staff = $staffModel->getStaffById($id);
        echo json_encode($staff);
        exit();
    }

    /**
     * =================================================================
     * PROSES UPDATE STAFF
     * =================================================================
     */
    public function ubahStaff() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $staffModel = new Staff_model();
            if ($staffModel->updateStaff($_POST) >= 0) {
                Flasher::setFlash('Berhasil!', 'Data staff berhasil diubah.', 'success');
            } else {
                Flasher::setFlash('Gagal!', 'Gagal mengubah data staff.', 'danger');
            }
        }
        header('Location: ' . BASEURL . '/admin/pengguna/staff');
        exit;
    }

    /**
     * =================================================================
     * PROSES HAPUS STAFF MASSAL
     * =================================================================
     */
    public function hapusStaffMassal() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['ids'])) {
            $ids = $_POST['ids'];
            $staffModel = new Staff_model();
            $rowCount = $staffModel->hapusStaffMassal($ids);

            if ($rowCount > 0) {
                Flasher::setFlash('Berhasil!', "{$rowCount} data staff berhasil dihapus.", 'success');
            } else {
                Flasher::setFlash('Gagal!', 'Tidak ada data staff yang dihapus.', 'danger');
            }
        } else {
            Flasher::setFlash('Gagal!', 'Tidak ada data yang dipilih untuk dihapus.', 'danger');
        }
        header('Location: ' . BASEURL . '/admin/pengguna/staff');
        exit;
    }

    /**
     * =================================================================
     * PROSES TAMBAH GURU BARU
     * =================================================================
     */
    public function tambahGuru() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $guruModel = new Guru_model();
            
            if ($guruModel->createGuruAndUserAccount($_POST) > 0) {
                Flasher::setFlash('Berhasil!', 'Data guru berhasil ditambahkan.', 'success');
            } else {
                Flasher::setFlash('Gagal!', 'Gagal menambahkan guru. Pastikan NIP unik.', 'danger');
            }
        }
        header('Location: ' . BASEURL . '/admin/pengguna/guru');
        exit;
    }

    /**
     * =================================================================
     * PROSES IMPORT GURU DARI FILE CSV
     * =================================================================
     */
    public function importGuru() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file_import_guru'])) {
            $file = $_FILES['file_import_guru'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                Flasher::setFlash('Gagal!', 'Terjadi error saat mengunggah file.', 'danger');
            } elseif (strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) != 'csv') {
                Flasher::setFlash('Gagal!', 'Hanya file format .csv yang didukung.', 'danger');
            } else {
                $fileHandle = fopen($file['tmp_name'], 'r');
                fgetcsv($fileHandle); // Lewati header

                $dataUntukImport = [];
                while (($row = fgetcsv($fileHandle)) !== FALSE) {
                    if (!empty($row[0]) && !empty($row[1])) {
                        $dataUntukImport[] = [
                            'nama'          => trim($row[0]),
                            'nip'           => trim($row[1]),
                            'jenis_kelamin' => trim($row[2] ?? 'Laki-laki'),
                            'no_hp'         => trim($row[3] ?? null),
                            'email'         => trim($row[4] ?? null),
                            'ttl'           => null, 'agama' => null, 'alamat' => null
                        ];
                    }
                }
                fclose($fileHandle);
                
                if (!empty($dataUntukImport)) {
                    $guruModel = new Guru_model();
                    $hasil = $guruModel->tambahGuruBatch($dataUntukImport);

                    if ($hasil['failed'] > 0) {
                        $pesan = "{$hasil['success']} data berhasil diimpor, {$hasil['failed']} data gagal. <br> Detail: <br>" . implode('<br>', $hasil['errors']);
                        Flasher::setFlash('Proses Selesai dengan Error', $pesan, 'danger');
                    } else {
                        Flasher::setFlash('Berhasil!', "{$hasil['success']} data guru berhasil diimpor.", 'success');
                    }
                } else {
                    Flasher::setFlash('Gagal!', 'File CSV kosong atau formatnya salah.', 'danger');
                }
            }
        } else {
            Flasher::setFlash('Gagal!', 'Tidak ada file yang diunggah.', 'danger');
        }

        header('Location: ' . BASEURL . '/admin/pengguna/guru');
        exit;
    }

    /**
     * =================================================================
     * ENDPOINT AJAX UNTUK PENCARIAN GURU
     * =================================================================
     */
    public function searchGuru() {
        header('Content-Type: application/json');
        $keyword = $_GET['keyword'] ?? '';
        $guruModel = new Guru_model();
        $guruData = $guruModel->getGuruPaginated(0, 999, $keyword); 
        echo json_encode($guruData);
        exit();
    }

     /**
     * =================================================================
     * PROSES HAPUS GURU
     * =================================================================
     */
    public function hapusGuru($id) {
        $guruModel = new Guru_model();
        if ($guruModel->hapusGuru($id) > 0) {
            Flasher::setFlash('Berhasil!', 'Data guru berhasil dihapus.', 'success');
        } else {
            Flasher::setFlash('Gagal!', 'Gagal menghapus data guru.', 'danger');
        }
        header('Location: ' . BASEURL . '/admin/pengguna/guru');
        exit;
    }

    /**
     * =================================================================
     * ENDPOINT AJAX UNTUK MENGAMBIL DATA GURU BY ID
     * =================================================================
     */
    public function getGuruById($id) {
        header('Content-Type: application/json');
        $guruModel = new Guru_model();
        $guru = $guruModel->getGuruById($id);
        echo json_encode($guru);
        exit();
    }
    
    /**
     * =================================================================
     * PROSES UPDATE GURU
     * =================================================================
     */
    public function ubahGuru() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $guruModel = new Guru_model();
            $result = $guruModel->updateGuru($_POST);

            if ($result > 0) {
                Flasher::setFlash('Berhasil!', 'Data guru berhasil diubah.', 'success');
            } else if ($result === 0) {
                Flasher::setFlash('Info', 'Tidak ada perubahan data yang disimpan.', 'danger');
            } else {
                Flasher::setFlash('Gagal!', 'Terjadi kesalahan saat mengubah data.', 'danger');
            }
        }
        header('Location: ' . BASEURL . '/admin/pengguna/guru');
        exit;
    }

    /**
     * =================================================================
     * MENAMPILKAN HALAMAN DETAIL GURU
     * =================================================================
     */
    public function detailGuru($id) {
        $guruModel = new Guru_model();
        $data = [
            'title' => 'Detail Guru',
            'guru' => $guruModel->getGuruById($id)
        ];
        // Memuat view admin/guru/detail.php dan mengirimkan data guru
        $this->view('admin/guru/detail', $data);
    }

    /**
     * =================================================================
     * PROSES HAPUS GURU MASSAL
     * =================================================================
     */
    public function hapusGuruMassal() {
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
        header('Location: ' . BASEURL . '/admin/pengguna/guru');
        exit;
    }

     /**
     * =================================================================
     * MENAMPILKAN HALAMAN DETAIL STAFF
     * =================================================================
     */
    public function detailStaff($id) {
        $staffModel = new Staff_model();
        $data = [
            'title' => 'Detail Staff',
            'staff' => $staffModel->getStaffById($id)
        ];
        $this->view('admin/staff/detail', $data);
    }
/**
     * =================================================================
     * PROSES TAMBAH SISWA BARU (DIPERBARUI DENGAN UPLOAD FOTO)
     * =================================================================
     */
    public function tambahSiswa() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            // --- LOGIKA UPLOAD FOTO ---
            $namaFoto = 'default.png'; // Nama default jika tidak ada foto diupload
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['foto'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $namaFotoBaru = uniqid('siswa_') . '.' . $ext;
                $targetDir = APP_ROOT . '/public/img/siswa/';
                
                // Pindahkan file dari lokasi sementara ke folder tujuan
                if (move_uploaded_file($file['tmp_name'], $targetDir . $namaFotoBaru)) {
                    $namaFoto = $namaFotoBaru;
                }
            }
            // --- AKHIR LOGIKA UPLOAD FOTO ---

            $data = $_POST;
            $data['foto'] = $namaFoto; // Tambahkan nama file foto ke data yang akan disimpan

            $siswaModel = new Siswa_model();
            if ($siswaModel->createSiswaAndUserAccount($data) > 0) {
                Flasher::setFlash('Berhasil!', 'Data siswa berhasil ditambahkan.', 'success');
            } else {
                Flasher::setFlash('Gagal!', 'Gagal menambahkan siswa. Pastikan ID Siswa (NIS) unik.', 'danger');
            }
        }
        header('Location: ' . BASEURL . '/admin/pengguna/siswa');
        exit;
    }
    
    /**
     * =================================================================
     * PROSES HAPUS SISWA
     * =================================================================
     */
    public function hapusSiswa($id) {
        $siswaModel = new Siswa_model();
        if ($siswaModel->hapusSiswa($id) > 0) {
            Flasher::setFlash('Berhasil!', 'Data siswa berhasil dihapus.', 'success');
        } else {
            Flasher::setFlash('Gagal!', 'Gagal menghapus data siswa.', 'danger');
        }
        header('Location: ' . BASEURL . '/admin/pengguna/siswa');
        exit;
    }

    /**
     * =================================================================
     * MENAMPILKAN HALAMAN DETAIL SISWA
     * =================================================================
     */
    public function detailSiswa($id) {
    $siswaModel = new Siswa_model();
    $data = [
        'title' => 'Detail Siswa',
        'siswa' => $siswaModel->getSiswaById($id),
        'origin' => $_GET['origin'] ?? null, // Mengambil parameter origin
        'kelas_id' => $_GET['kelas_id'] ?? null // Mengambil ID kelas jika ada
    ];
    $this->view('admin/siswa/detail', $data);
}
    /**
     * =================================================================
     * PROSES UPDATE SISWA
     * =================================================================
     */
    public function ubahSiswa() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $siswaModel = new Siswa_model();

            $data = $_POST;
            $data['foto'] = $_POST['foto_lama'];
            
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['foto'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $namaFotoBaru = uniqid('siswa_') . '.' . $ext;
                $targetDir = APP_ROOT . '/public/img/siswa/';
                
                if (move_uploaded_file($file['tmp_name'], $targetDir . $namaFotoBaru)) {
                    $data['foto'] = $namaFotoBaru;
                    if ($data['foto_lama'] && $data['foto_lama'] !== 'default.png') {
                        @unlink($targetDir . $data['foto_lama']);
                    }
                }
            }

            $result = $siswaModel->updateSiswa($data);
            if ($result > 0) {
                Flasher::setFlash('Berhasil!', 'Data siswa berhasil diubah.', 'success');
            } else if ($result === 0) {
                Flasher::setFlash('Info', 'Tidak ada perubahan data yang disimpan.', 'danger');
            } else {
                Flasher::setFlash('Gagal!', 'Terjadi kesalahan saat mengubah data.', 'danger');
            }
        }
        header('Location: ' . BASEURL . '/admin/pengguna/siswa');
        exit;
    }
    
    /**
     * =================================================================
     * PROSES HAPUS SISWA MASSAL
     * =================================================================
     */
    public function hapusSiswaMassal() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['ids'])) {
            $ids = $_POST['ids'];
            $siswaModel = new Siswa_model();
            $rowCount = $siswaModel->hapusSiswaMassal($ids);
            if ($rowCount > 0) {
                Flasher::setFlash('Berhasil!', "{$rowCount} data siswa berhasil dihapus.", 'success');
            } else {
                Flasher::setFlash('Gagal!', 'Tidak ada data siswa yang dihapus.', 'danger');
            }
        } else {
            Flasher::setFlash('Gagal!', 'Tidak ada data yang dipilih untuk dihapus.', 'danger');
        }
        header('Location: ' . BASEURL . '/admin/pengguna/siswa');
        exit;
    }

    /**
     * =================================================================
     * PROSES IMPORT SISWA DARI FILE CSV
     * =================================================================
     */
    public function importSiswa() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file_import_siswa'])) {
            $file = $_FILES['file_import_siswa'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                Flasher::setFlash('Gagal!', 'Terjadi error saat mengunggah file.', 'danger');
            } elseif (strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) != 'csv') {
                Flasher::setFlash('Gagal!', 'Hanya file format .csv yang didukung.', 'danger');
            } else {
                $fileHandle = fopen($file['tmp_name'], 'r');
                fgetcsv($fileHandle);

                $dataUntukImport = [];
                while (($row = fgetcsv($fileHandle)) !== FALSE) {
                    if (!empty($row[0]) && !empty($row[1])) {
                        $dataUntukImport[] = [
                            'nama'          => trim($row[0]),
                            'id_siswa'      => trim($row[1]),
                            'jenis_kelamin' => trim($row[2] ?? 'Laki-laki'),
                            'no_hp'         => trim($row[3] ?? null),
                            'email'         => trim($row[4] ?? null),
                            'ttl'           => null,
                            'agama'         => null,
                            'alamat'        => null,
                            'foto'          => 'default.png'
                        ];
                    }
                }
                fclose($fileHandle);
                
                if (!empty($dataUntukImport)) {
                    $siswaModel = new Siswa_model();
                    $hasil = $siswaModel->importSiswaBatch($dataUntukImport);

                    if ($hasil['failed'] > 0) {
                        $pesan = "{$hasil['success']} data berhasil diimpor, {$hasil['failed']} data gagal. <br> Detail Error: <br>" . implode('<br>', $hasil['errors']);
                        Flasher::setFlash('Proses Selesai dengan Error', $pesan, 'danger');
                    } else {
                        Flasher::setFlash('Berhasil!', "{$hasil['success']} data siswa berhasil diimpor.", 'success');
                    }
                } else {
                    Flasher::setFlash('Gagal!', 'File CSV kosong atau formatnya salah.', 'danger');
                }
            }
        } else {
            Flasher::setFlash('Gagal!', 'Tidak ada file yang diunggah.', 'danger');
        }

        header('Location: ' . BASEURL . '/admin/pengguna/siswa');
        exit;
    }
    
    /**
     * =================================================================
     * PROSES UBAH PASSWORD AKUN
     * =================================================================
     */
    public function ubahPasswordAkun() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id']) && isset($_POST['new_password'])) {
            $id = $_POST['id'];
            $newPassword = $_POST['new_password'];
            $confirmPassword = $_POST['confirm_password'];

            if ($newPassword !== $confirmPassword) {
                Flasher::setFlash('Gagal!', 'Konfirmasi kata sandi tidak cocok.', 'danger');
            } elseif (strlen($newPassword) < 6) {
                Flasher::setFlash('Gagal!', 'Kata sandi baru minimal harus 6 karakter.', 'danger');
            } else {
                $userModel = new User_model();
                if ($userModel->changePassword($id, $newPassword) > 0) {
                    Flasher::setFlash('Berhasil!', 'Kata sandi berhasil diubah.', 'success');
                } else {
                    Flasher::setFlash('Gagal!', 'Gagal mengubah kata sandi.', 'danger');
                }
            }
        } else {
            Flasher::setFlash('Gagal!', 'Permintaan tidak valid.', 'danger');
        }
        header('Location: ' . BASEURL . '/admin/pengguna/akun');
        exit;
    }
    
    /**
     * =================================================================
     * PROSES UBAH KELAS
     * =================================================================
     */
    public function ubahKelas() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $kelasModel = new Kelas_model();
            if ($kelasModel->updateKelas($_POST) > 0) {
                Flasher::setFlash('Berhasil!', 'Data kelas berhasil diubah.', 'success');
            } else {
                Flasher::setFlash('Gagal!', 'Gagal mengubah data kelas.', 'danger');
            }
        }
        header('Location: ' . BASEURL . '/admin/kelas');
        exit;
    }

    /**
     * =================================================================
     * ENDPOINT AJAX GET KELAS BY ID
     * =================================================================
     */
    public function getKelasById($id) {
        header('Content-Type: application/json');
        $kelasModel = new Kelas_model();
        $kelas = $kelasModel->getKelasById($id);
        echo json_encode($kelas);
        exit;
    }

    /**
     * =================================================================
     * PROSES HAPUS KELAS MASSAL
     * =================================================================
     */
    public function hapusKelasMassal() {
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
        header('Location: ' . BASEURL . '/admin/kelas');
        exit;
    }
    
    /**
     * =================================================================
     * MENAMPILKAN HALAMAN DETAIL KELAS
     * =================================================================
     */
    public function detailKelas($kelasId, $halaman = 1) {
        $kelasModel = new Kelas_model();
        $siswaModel = new Siswa_model();
    
        $kelas = $kelasModel->getKelasById($kelasId);
    
        $limit = 10;
        $halaman = max(1, (int)$halaman);
        $offset = ($halaman - 1) * $limit;
    
        $siswa = [];
        $totalSiswa = 0;
        $totalHalaman = 1;
        $searchTerm = null;
    
        if ($kelas) {
            $searchTerm = $_GET['search'] ?? null;
            $siswa = $siswaModel->getSiswaByKelasIdPaginated($kelas['id'], $offset, $limit, $searchTerm);
            $totalSiswa = $siswaModel->countSiswaByKelasId($kelas['id'], $searchTerm);
            $totalHalaman = ceil($totalSiswa / $limit);
        }
    
        $data = [
            'title' => 'Detail Kelas',
            'kelas' => $kelas,
            'siswa' => $siswa,
            'total_halaman' => $totalHalaman,
            'halaman_aktif' => (int)$halaman,
            'search_term' => $searchTerm,
            'unassigned_siswa' => $siswaModel->getUnassignedSiswa()
        ];
    
        $this->view('admin/kelas/detail', $data);
    }
    
    /**
     * =================================================================
     * ENDPOINT AJAX UNTUK PENCARIAN SISWA YANG BELUM ADA KELAS
     * =================================================================
     */
      public function searchUnassignedSiswa() {
    header('Content-Type: application/json');
    $keyword = $_GET['keyword'] ?? null;
    $siswaModel = new Siswa_model();
    
    // Memanggil metode baru di model untuk mencari siswa yang belum punya kelas
    $siswaData = $siswaModel->getUnassignedSiswaByKeyword($keyword);
    
    echo json_encode($siswaData);
    exit();
}

    /**
     * =================================================================
     * PROSES ASSIGN SISWA KE KELAS
     * =================================================================
     */
    public function assignSiswaToKelas() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['siswa_id']) && isset($_POST['kelas_id'])) {
            $siswaModel = new Siswa_model();
            $siswaId = $_POST['siswa_id'];
            $kelasId = $_POST['kelas_id'];
            
            $rowCount = $siswaModel->assignSiswaToKelas($siswaId, $kelasId);
            
            if ($rowCount > 0) {
                Flasher::setFlash('Berhasil!', 'Siswa berhasil ditambahkan ke kelas.', 'success');
            } else {
                Flasher::setFlash('Gagal!', 'Tidak ada siswa yang ditambahkan ke kelas. Mungkin siswa sudah memiliki kelas.', 'danger');
            }
        } else {
            Flasher::setFlash('Gagal!', 'Permintaan tidak valid.', 'danger');
        }
        header('Location: ' . BASEURL . '/admin/detailKelas/' . $_POST['kelas_id']);
        exit;
    }
    
    /**
     * =================================================================
     * PROSES IMPORT SISWA KE KELAS (BARU)
     * =================================================================
     */
    public function importSiswaKeKelas() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file_import_siswa']) && isset($_POST['kelas_id'])) {
            $file = $_FILES['file_import_siswa'];
            $kelasId = $_POST['kelas_id'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                Flasher::setFlash('Gagal!', 'Terjadi error saat mengunggah file.', 'danger');
            } elseif (strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) != 'csv') {
                Flasher::setFlash('Gagal!', 'Hanya file format .csv yang didukung.', 'danger');
            } else {
                $fileHandle = fopen($file['tmp_name'], 'r');
                fgetcsv($fileHandle); // Lewati baris header

                $nises_to_find = [];
                $line = 2;
                while (($row = fgetcsv($fileHandle)) !== FALSE) {
                    $nis = trim($row[0]);
                    if (!empty($nis)) {
                        $nises_to_find[] = $nis;
                    }
                    $line++;
                }
                fclose($fileHandle);
                
                if (!empty($nises_to_find)) {
                    $siswaModel = new Siswa_model();
                    $siswa_valid = $siswaModel->getSiswaByNisInBatch($nises_to_find);
                    $found_nises = array_column($siswa_valid, 'id_siswa');
                    
                    $siswa_to_assign = array_column($siswa_valid, 'id');
                    $rowCount = $siswaModel->assignSiswaBatchToKelas($siswa_to_assign, $kelasId);
                    
                    $not_found_nises = array_diff($nises_to_find, $found_nises);
                    $not_found_message = '';
                    if (!empty($not_found_nises)) {
                         $not_found_message = 'Berikut adalah ID Siswa (NIS) yang tidak ditemukan: ' . implode(', ', $not_found_nises);
                    }
                    
                    if ($rowCount > 0) {
                        $message = "{$rowCount} siswa berhasil ditambahkan ke kelas.";
                        if (!empty($not_found_message)) {
                            $message .= "<br>" . $not_found_message;
                        }
                        Flasher::setFlash('Berhasil!', $message, 'success');
                    } else if (!empty($not_found_message)) {
                        Flasher::setFlash('Proses Gagal!', "Tidak ada siswa yang berhasil ditambahkan. <br>" . $not_found_message, 'danger');
                    } else {
                        Flasher::setFlash('Gagal!', 'File CSV kosong atau tidak memiliki data yang valid.', 'danger');
                    }

                } else {
                    Flasher::setFlash('Gagal!', 'File CSV kosong atau tidak memiliki data yang valid.', 'danger');
                }
            }
        } else {
            Flasher::setFlash('Gagal!', 'Permintaan tidak valid.', 'danger');
        }

        header('Location: ' . BASEURL . '/admin/detailKelas/' . $_POST['kelas_id']);
        exit;
    }

    /**
     * =================================================================
     * PROSES HAPUS SISWA DARI KELAS
     * =================================================================
     */
    public function hapusSiswaDariKelas($siswaId) {
        $siswaModel = new Siswa_model();
        $siswa = $siswaModel->getSiswaById($siswaId);
        
        if (!$siswa) {
            Flasher::setFlash('Gagal!', 'Siswa tidak ditemukan.', 'danger');
            header('Location: ' . BASEURL . '/admin/kelas');
            exit;
        }

        $kelasId = $siswa['kelas_id'];

        if ($siswaModel->removeSiswaFromKelas($siswaId) > 0) {
            Flasher::setFlash('Berhasil!', 'Siswa berhasil dikeluarkan dari kelas.', 'success');
        } else {
            Flasher::setFlash('Gagal!', 'Gagal mengeluarkan siswa dari kelas.', 'danger');
        }

        header('Location: ' . BASEURL . '/admin/detailKelas/' . $kelasId);
        exit;
    }

    /**
     * =================================================================
     * PROSES MENGELUARKAN SISWA DARI KELAS SECARA MASSAL (BARU)
     * =================================================================
     */
    public function removeSiswaDariKelasMassal() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['ids']) && !empty($_POST['kelas_id'])) {
            $ids = $_POST['ids'];
            $kelasId = $_POST['kelas_id'];
            $siswaModel = new Siswa_model();
            $rowCount = $siswaModel->removeSiswaFromKelasMassal($ids);
            
            if ($rowCount > 0) {
                Flasher::setFlash('Berhasil!', "{$rowCount} siswa berhasil dikeluarkan dari kelas.", 'success');
            } else {
                Flasher::setFlash('Gagal!', 'Tidak ada siswa yang dikeluarkan dari kelas.', 'danger');
            }
            header('Location: ' . BASEURL . '/admin/detailKelas/' . $kelasId);
            exit;
        } else {
            Flasher::setFlash('Gagal!', 'Permintaan tidak valid.', 'danger');
            header('Location: ' . BASEURL . '/admin/kelas');
            exit;
        }
    }
   /* =================================================================
 * ENDPOINT AJAX UNTUK MENGAMBIL DATA SISWA BY ID
 * =================================================================
 */
public function getSiswaById($id) {
    header('Content-Type: application/json');
    
    // Tambahkan ob_clean() untuk membersihkan output buffer
    // Ini akan menghapus output tidak terduga (seperti pesan Flasher atau error PHP)
    ob_clean();
    
    $siswaModel = new Siswa_model();
    $siswa = $siswaModel->getSiswaById($id);
    echo json_encode($siswa);
    exit();
}
  
}
  
