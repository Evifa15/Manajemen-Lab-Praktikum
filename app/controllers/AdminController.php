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
     * Menampilkan halaman dashboard utama untuk admin.
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
        $data = [ 'title' => 'Manajemen Kelas', 'all_guru' => [] ];
        $this->view('admin/manajemen_kelas', $data);
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
     * Menerima data dari form modal, mengirim ke model untuk disimpan,
     * dan memberikan notifikasi (flash message).
     */
    public function tambahStaff() {
        // $this->checkAuth(); // Diaktifkan nanti

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
     * Method ini bertugas untuk memuat file view beserta header dan footer.
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
     * Menangani upload file CSV, mem-parsing datanya, dan mengirim ke model.
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

    // Letakkan method ini di dalam class AdminController

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
        // Arahkan kembali ke halaman manajemen pengguna tab guru
        header('Location: ' . BASEURL . '/admin/pengguna/guru');
        exit;
    }

    // Letakkan method ini di dalam class AdminController

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

            // Kondisi ini sekarang akan bekerja dengan benar
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
     * ENDPOINT AJAX UNTUK MENGAMBIL DATA SISWA BY ID
     * =================================================================
     */
    public function getSiswaById($id) {
        header('Content-Type: application/json');
        $siswaModel = new Siswa_model();
        $siswa = $siswaModel->getSiswaById($id);
        echo json_encode($siswa);
        exit();
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
            'siswa' => $siswaModel->getSiswaById($id)
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
    
    // =================================================================
    // PROSES UBAH PASSWORD AKUN
    // =================================================================
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
    
    // --- Kumpulan method kosong untuk fitur yang belum dibuat ---
    public function tambahBarang() {}
    public function ubahBarang() {}
    public function hapusBarang($id) {}
    public function tambahKelas() {}
    public function ubahKelas() {}
    public function hapusKelas($id) {}
    public function detailKelas() {}

}