<?php

class Siswa_model {
    private $table = 'siswa';
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    /**
     * ==========================================================
     * FUNGSI TAMBAH SISWA & BUAT AKUN (DIPERBARUI)
     * ==========================================================
     */
    public function createSiswaAndUserAccount($data) {
        $this->db->beginTransaction();

        try {
            $hashed_password = password_hash($data['id_siswa'], PASSWORD_DEFAULT);
            $this->db->query('INSERT INTO users (username, password, role) VALUES (:username, :password, :role)');
            $this->db->bind(':username', $data['nama']);
            $this->db->bind(':password', $hashed_password);
            $this->db->bind(':role', 'siswa');
            $this->db->execute();
            $userId = $this->db->lastInsertId();

            // ✅ Perbaikan: Normalisasi 'jenis_kelamin' sebelum disimpan
            $data['jenis_kelamin'] = str_replace('-', ' ', $data['jenis_kelamin']);

            $query = "INSERT INTO siswa (user_id, id_siswa, nama, jenis_kelamin, ttl, agama, alamat, no_hp, email, foto, kelas_id) 
                      VALUES (:user_id, :id_siswa, :nama, :jenis_kelamin, :ttl, :agama, :alamat, :no_hp, :email, :foto, NULL)";
            
            $this->db->query($query);
            $this->db->bind('user_id', $userId);
            $this->db->bind('id_siswa', $data['id_siswa']);
            $this->db->bind('nama', $data['nama']);
            $this->db->bind('jenis_kelamin', $data['jenis_kelamin']);
            $this->db->bind('no_hp', $data['no_hp']);
            $this->db->bind('email', $data['email']);
            $this->db->bind('ttl', $data['ttl']);
            $this->db->bind('agama', $data['agama']);
            $this->db->bind('alamat', $data['alamat']);
            
            $this->db->bind('foto', $data['foto']);
            
            $this->db->execute();
            $this->db->commit();
            return $this->db->rowCount();

        } catch (Exception $e) {
            $this->db->rollBack();
            return 0;
        }
    }

    /**
     * ==========================================================
     * FUNGSI GET & COUNT SEMUA SISWA (MASTER DATA)
     * ==========================================================
     */
    public function getAllSiswaPaginated($offset, $limit, $keyword = null) {
        $sql = 'SELECT id, id_siswa, nama, jenis_kelamin, no_hp FROM ' . $this->table;
        if (!empty($keyword)) {
            $sql .= ' WHERE nama LIKE :keyword OR id_siswa LIKE :keyword OR jenis_kelamin LIKE :keyword OR no_hp LIKE :keyword';
        }
        $sql .= ' ORDER BY nama ASC LIMIT :limit OFFSET :offset';
        
        $this->db->query($sql);
        if (!empty($keyword)) {
            $this->db->bind(':keyword', '%' . $keyword . '%');
        }
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function countAllSiswa($keyword = null) {
        $sql = 'SELECT COUNT(id) as total FROM ' . $this->table;
        if (!empty($keyword)) {
            $sql .= ' WHERE nama LIKE :keyword OR id_siswa LIKE :keyword OR jenis_kelamin LIKE :keyword OR no_hp LIKE :keyword';
        }
        $this->db->query($sql);
        if (!empty($keyword)) {
            $this->db->bind(':keyword', '%' . $keyword . '%');
        }
        $result = $this->db->single();
        return $result ? (int)$result['total'] : 0;
    }
    
    /**
     * ==========================================================
     * FUNGSI GET SISWA BY ID
     * ==========================================================
     */
    public function getSiswaById($id) {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    /**
     * ==========================================================
     * FUNGSI UPDATE SISWA
     * ==========================================================
     */
    public function updateSiswa($data) {
        $this->db->beginTransaction();
        try {
            // ✅ Perbaikan: Normalisasi 'jenis_kelamin' sebelum disimpan
            $data['jenis_kelamin'] = str_replace('-', ' ', $data['jenis_kelamin']);

            $query = "UPDATE " . $this->table . " SET
                        id_siswa = :id_siswa, nama = :nama, jenis_kelamin = :jenis_kelamin,
                        ttl = :ttl, agama = :agama, alamat = :alamat,
                        no_hp = :no_hp, email = :email, foto = :foto
                      WHERE id = :id";
            
            $this->db->query($query);
            $this->db->bind('id', $data['id']);
            $this->db->bind('id_siswa', $data['id_siswa']);
            $this->db->bind('nama', $data['nama']);
            $this->db->bind('jenis_kelamin', $data['jenis_kelamin']);
            $this->db->bind('ttl', $data['ttl']);
            $this->db->bind('agama', $data['agama']);
            $this->db->bind('alamat', $data['alamat']);
            $this->db->bind('no_hp', $data['no_hp']);
            $this->db->bind('email', $data['email']);
            $this->db->bind('foto', $data['foto']);
            $this->db->execute();
            $rowCount = $this->db->rowCount();

            $siswa = $this->getSiswaById($data['id']);
            if ($siswa && $siswa['user_id']) {
                $this->db->query('UPDATE users SET username = :username WHERE id = :user_id');
                $this->db->bind(':username', $data['nama']);
                $this->db->bind(':user_id', $siswa['user_id']);
                $this->db->execute();
            }

            $this->db->commit();
            return $rowCount;

        } catch (Exception $e) {
            $this->db->rollBack();
            return -1;
        }
    }
    
    public function hapusSiswa($id) {
        $siswa = $this->getSiswaById($id);
        if (!$siswa) return 0;

        $this->db->beginTransaction();
        try {
            $this->db->query('DELETE FROM ' . $this->table . ' WHERE id = :id');
            $this->db->bind('id', $id);
            $this->db->execute();
            $rowCount = $this->db->rowCount();

            if ($siswa['user_id']) {
                $this->db->query('DELETE FROM users WHERE id = :user_id');
                $this->db->bind('user_id', $siswa['user_id']);
                $this->db->execute();
            }

            $this->db->commit();
            return $rowCount;

        } catch (Exception $e) {
            $this->db->rollBack();
            return 0;
        }
    }

    /**
     * ==========================================================
     * FUNGSI HAPUS SISWA MASSAL
     * ==========================================================
     */
    public function hapusSiswaMassal($ids) {
        if (empty($ids)) {
            return 0;
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $this->db->beginTransaction();
        try {
            $this->db->query("SELECT user_id FROM {$this->table} WHERE id IN ({$placeholders}) AND user_id IS NOT NULL");
            foreach ($ids as $k => $id) {
                $this->db->bind($k + 1, $id);
            }
            $users_to_delete = $this->db->resultSet();
            $user_ids = array_column($users_to_delete, 'user_id');

            $this->db->query("DELETE FROM {$this->table} WHERE id IN ({$placeholders})");
            foreach ($ids as $k => $id) {
                $this->db->bind($k + 1, $id);
            }
            $this->db->execute();
            $rowCount = $this->db->rowCount();

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
    
    /**
     * ==========================================================
     * FUNGSI IMPORT SISWA BATCH
     * ==========================================================
     * Mengimpor data siswa secara massal dari array (hasil parse CSV).
     */
    public function importSiswaBatch($dataSiswa) {
        if (empty($dataSiswa)) {
            return ['success' => 0, 'failed' => 0, 'errors' => ['Tidak ada data untuk diimpor.']];
        }

        $berhasil = 0;
        $gagal = 0;
        $errors = [];

        foreach ($dataSiswa as $index => $siswa) {
            if ($this->createSiswaAndUserAccount($siswa) > 0) {
                $berhasil++;
            } else {
                $gagal++;
                $errors[] = "Baris " . ($index + 2) . ": Gagal menyimpan. Kemungkinan ID Siswa (NIS) '{$siswa['id_siswa']}' sudah ada.";
            }
        }
        
        return ['success' => $berhasil, 'failed' => $gagal, 'errors' => $errors];
    }
}