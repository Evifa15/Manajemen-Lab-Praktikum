<?php

class Siswa_model {
    private $table = 'siswa';
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    /**
     * ==========================================================
     * METODE BARU: Untuk membuat akun user dan profil siswa
     * dalam satu transaksi.
     * ==========================================================
     */
    public function createSiswaAndUserAccount($data) {
        $this->db->beginTransaction();

        try {
            // Langkah 1: Buat entri di tabel 'users' terlebih dahulu
            // PERINGATAN KEAMANAN: Menggunakan ID Siswa sebagai password tidak aman.
            // Di sini kita langsung melakukan hashing untuk menyimpannya di database.
            $hashed_password = password_hash($data['id_siswa'], PASSWORD_DEFAULT);
            
            $this->db->query('INSERT INTO users (username, password, role) VALUES (:username, :password, :role)');
            $this->db->bind(':username', $data['nama']); // Nama siswa sebagai username
            $this->db->bind(':password', $hashed_password);
            $this->db->bind(':role', 'siswa'); // Role sudah pasti 'siswa'
            $this->db->execute();
            
            // Ambil ID dari user yang baru saja dibuat untuk dihubungkan ke tabel siswa
            $userId = $this->db->lastInsertId();

            // Langkah 2: Buat entri di tabel 'siswa' dengan user_id yang sudah didapat
            $query = "INSERT INTO siswa (user_id, kelas_id, id_siswa, nama, jenis_kelamin, status, ttl, agama, alamat, no_hp, email, foto) 
                      VALUES (:user_id, :kelas_id, :id_siswa, :nama, :jenis_kelamin, :status, :ttl, :agama, :alamat, :no_hp, :email, :foto)";
            
            $this->db->query($query);
            $this->db->bind('user_id', $userId); // Ini adalah penghubungnya
            $this->db->bind('kelas_id', $data['kelas_id']);
            $this->db->bind('id_siswa', $data['id_siswa']);
            $this->db->bind('nama', $data['nama']);
            $this->db->bind('jenis_kelamin', $data['jenis_kelamin']);
            $this->db->bind('status', $data['status']);
            $this->db->bind('ttl', $data['ttl']);
            $this->db->bind('agama', $data['agama']);
            $this->db->bind('alamat', $data['alamat']);
            $this->db->bind('no_hp', $data['no_hp']);
            $this->db->bind('email', $data['email']);
            $this->db->bind('foto', $data['foto']);
            
            $this->db->execute();
            
            // Jika semua berhasil, konfirmasi transaksi
            $this->db->commit();
            return $this->db->rowCount();

        } catch (Exception $e) {
            // Jika ada satu saja yang gagal, batalkan semua perubahan
            $this->db->rollBack();
            // Anda bisa mencatat error $e->getMessage() jika perlu untuk debug
            return 0;
        }
    }

    public function getSiswaByKelasIdPaginated($kelas_id, $offset, $limit, $keyword = null) {
        // ✅ PERBAIKAN: Tambahkan 'jenis_kelamin' dan 'status' ke kondisi pencarian
        $sql = 'SELECT id, id_siswa, nama, jenis_kelamin, status FROM ' . $this->table . ' WHERE kelas_id = :kelas_id';
        if (!empty($keyword)) {
            $sql .= ' AND (nama LIKE :keyword OR id_siswa LIKE :keyword OR jenis_kelamin LIKE :keyword OR status LIKE :keyword)';
        }
        $sql .= ' ORDER BY nama ASC LIMIT :limit OFFSET :offset';
        
        $this->db->query($sql);
        $this->db->bind(':kelas_id', $kelas_id);
        if (!empty($keyword)) {
            $this->db->bind(':keyword', '%' . $keyword . '%');
        }
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function countAllSiswaByKelasId($kelas_id, $keyword = null) {
        // ✅ PERBAIKAN: Tambahkan 'jenis_kelamin' dan 'status' ke kondisi pencarian
        $sql = 'SELECT COUNT(*) as total FROM ' . $this->table . ' WHERE kelas_id = :kelas_id';
        if (!empty($keyword)) {
            $sql .= ' AND (nama LIKE :keyword OR id_siswa LIKE :keyword OR jenis_kelamin LIKE :keyword OR status LIKE :keyword)';
        }
        $this->db->query($sql);
        $this->db->bind(':kelas_id', $kelas_id);
        if (!empty($keyword)) {
            $this->db->bind(':keyword', '%' . $keyword . '%');
        }
        $result = $this->db->single();
        return $result ? (int)$result['total'] : 0;
    }

    public function getSiswaById($id) {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function tambahSiswa($data) {
        $query = "INSERT INTO siswa (kelas_id, id_siswa, nama, jenis_kelamin, status, ttl, agama, alamat, no_hp, email, foto) 
                  VALUES (:kelas_id, :id_siswa, :nama, :jenis_kelamin, :status, :ttl, :agama, :alamat, :no_hp, :email, :foto)";
        $this->db->query($query);
        $this->db->bind('kelas_id', $data['kelas_id']);
        $this->db->bind('id_siswa', $data['id_siswa']);
        $this->db->bind('nama', $data['nama']);
        $this->db->bind('jenis_kelamin', $data['jenis_kelamin']);
        $this->db->bind('status', $data['status']);
        $this->db->bind('ttl', $data['ttl']);
        $this->db->bind('agama', $data['agama']);
        $this->db->bind('alamat', $data['alamat']);
        $this->db->bind('no_hp', $data['no_hp']);
        $this->db->bind('email', $data['email']);
        $this->db->bind('foto', $data['foto']);
        
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function updateSiswa($data) {
        $query = "UPDATE siswa SET id_siswa = :id_siswa, nama = :nama, jenis_kelamin = :jenis_kelamin, status = :status, 
                  ttl = :ttl, agama = :agama, alamat = :alamat, no_hp = :no_hp, email = :email";
        if (!empty($data['foto'])) {
            $query .= ", foto = :foto";
        }
        $query .= " WHERE id = :id";
        
        $this->db->query($query);
        $this->db->bind('id', $data['id']);
        $this->db->bind('id_siswa', $data['id_siswa']);
        $this->db->bind('nama', $data['nama']);
        $this->db->bind('jenis_kelamin', $data['jenis_kelamin']);
        $this->db->bind('status', $data['status']);
        $this->db->bind('ttl', $data['ttl']);
        $this->db->bind('agama', $data['agama']);
        $this->db->bind('alamat', $data['alamat']);
        $this->db->bind('no_hp', $data['no_hp']);
        $this->db->bind('email', $data['email']);
        if (!empty($data['foto'])) {
            $this->db->bind('foto', $data['foto']);
        }

        $this->db->execute();
        return $this->db->rowCount();
    }

    public function hapusSiswa($id) {
        $this->db->query('DELETE FROM ' . $this->table . ' WHERE id = :id');
        $this->db->bind('id', $id);
        $this->db->execute();
        return $this->db->rowCount();
    }

     public function getSiswaByUserId($user_id) {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE user_id = :user_id');
        $this->db->bind(':user_id', $user_id);
        return $this->db->single();
    }

    /**
     * ==========================================================
     * METODE BARU: Untuk import data siswa secara massal.
     * ==========================================================
     */
    public function importSiswaBatch($dataSiswa, $kelas_id) {
        if (empty($dataSiswa)) {
            return ['success' => 0, 'failed' => 0, 'errors' => ['Tidak ada data untuk diimpor.']];
        }

        $berhasil = 0;
        $gagal = 0;
        $errors = [];

        foreach ($dataSiswa as $index => $siswa) {
            // Kita gunakan kembali logika createSiswaAndUserAccount untuk setiap baris
            $data = [
                'kelas_id'      => $kelas_id,
                'id_siswa'      => $siswa['id_siswa'],
                'nama'          => $siswa['nama'],
                'jenis_kelamin' => $siswa['jenis_kelamin'],
                'status'        => 'Murid', // Default status
                'ttl'           => null,
                'agama'         => null,
                'alamat'        => null,
                'no_hp'         => null,
                'email'         => null,
                'foto'          => 'default.png'
            ];

            // Panggil metode yang sudah ada di dalam model ini
            if ($this->createSiswaAndUserAccount($data) > 0) {
                $berhasil++;
            } else {
                $gagal++;
                $errors[] = "Baris " . ($index + 2) . ": Gagal menyimpan data. Kemungkinan ID Siswa '{$siswa['id_siswa']}' atau Nama '{$siswa['nama']}' sudah ada.";
            }
        }
        
        return ['success' => $berhasil, 'failed' => $gagal, 'errors' => $errors];
    }
    
    /**
     * ==========================================================
     * METODE BARU: Untuk menghapus data siswa dan akun user-nya
     * secara massal.
     * ==========================================================
     */
    public function hapusSiswaMassal($ids) {
        if (empty($ids)) {
            return 0;
        }
        
        // Buat placeholder sebanyak jumlah ID, contoh: (?,?,?)
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $this->db->beginTransaction();
        try {
            // 1. Ambil dulu user_id dari siswa yang akan dihapus
            $this->db->query("SELECT user_id FROM {$this->table} WHERE id IN ({$placeholders}) AND user_id IS NOT NULL");
            foreach ($ids as $k => $id) {
                $this->db->bind($k + 1, $id);
            }
            $users_to_delete = $this->db->resultSet();
            $user_ids = array_column($users_to_delete, 'user_id');

            // 2. Hapus dari tabel siswa
            $this->db->query("DELETE FROM {$this->table} WHERE id IN ({$placeholders})");
            foreach ($ids as $k => $id) {
                $this->db->bind($k + 1, $id);
            }
            $this->db->execute();
            $rowCount = $this->db->rowCount();

            // 3. Hapus dari tabel users jika ada user_id yang terhubung
            if (!empty($user_ids)) {
                $userPlaceholders = implode(',', array_fill(0, count($user_ids), '?'));
                $this->db->query("DELETE FROM users WHERE id IN ({$userPlaceholders})");
                 foreach ($user_ids as $k => $uid) {
                    $this->db->bind($k + 1, $uid);
                }
                $this->db->execute();
            }
            
            $this->db->commit();
            return $rowCount;

        } catch (Exception $e) {
            $this->db->rollBack();
            return 0;
        }
    }
}
