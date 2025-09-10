<?php

class Guru_model {
    private $table = 'guru';
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getAllGuru() {
        $this->db->query('SELECT id, nip, nama FROM ' . $this->table . ' ORDER BY nama ASC');
        return $this->db->resultSet();
    }

    public function getGuruPaginated($offset, $limit, $keyword = null) {
        $sql = 'SELECT id, nip, nama, jenis_kelamin, no_hp FROM ' . $this->table;
        if (!empty($keyword)) {
            $sql .= ' WHERE nama LIKE :keyword OR nip LIKE :keyword OR jenis_kelamin LIKE :keyword OR no_hp LIKE :keyword';
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

    public function countAllGuru($keyword = null) {
        $sql = 'SELECT COUNT(*) as total FROM ' . $this->table;
        if (!empty($keyword)) {
            $sql .= ' WHERE nama LIKE :keyword OR nip LIKE :keyword OR jenis_kelamin LIKE :keyword OR no_hp LIKE :keyword';
        }

        $this->db->query($sql);
        if (!empty($keyword)) {
            $this->db->bind(':keyword', '%' . $keyword . '%');
        }

        $result = $this->db->single();
        return $result ? (int)$result['total'] : 0;
    }

    public function getGuruById($id) {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getGuruByNip($nip) {
        $this->db->query('SELECT id FROM ' . $this->table . ' WHERE nip = :nip');
        $this->db->bind(':nip', $nip);
        return $this->db->single();
    }
    
    public function getGuruByUserId($user_id) {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE user_id = :user_id');
        $this->db->bind(':user_id', $user_id);
        return $this->db->single();
    }

    public function tambahGuru($data) {
        $query = "INSERT INTO guru (nip, nama, jenis_kelamin, ttl, agama, alamat, no_hp, email) 
                  VALUES (:nip, :nama, :jenis_kelamin, :ttl, :agama, :alamat, :no_hp, :email)";
        $this->db->query($query);
        $this->db->bind('nip', $data['nip']);
        $this->db->bind('nama', $data['nama']);
        $this->db->bind('jenis_kelamin', $data['jenis_kelamin']);
        $this->db->bind('ttl', $data['ttl']);
        $this->db->bind('agama', $data['agama']);
        $this->db->bind('alamat', $data['alamat']);
        $this->db->bind('no_hp', $data['no_hp']);
        $this->db->bind('email', $data['email']);
        
        $this->db->execute();
        return $this->db->rowCount();
    }
    
    public function tambahGuruBatch($dataGuru) {
    if (empty($dataGuru)) {
        return ['success' => 0, 'failed' => 0, 'errors' => []];
    }

    $this->db->beginTransaction();
    $berhasil = 0;
    $gagal = 0;
    $errors = [];

    // PERBAIKAN: Siapkan dua query, satu untuk 'users', satu untuk 'guru'
    $userQuery = "INSERT INTO users (username, password, role) VALUES (:username, :password, :role)";
    $guruQuery = "INSERT INTO guru (user_id, nama, nip, jenis_kelamin, no_hp, email, alamat) 
                  VALUES (:user_id, :nama, :nip, :jenis_kelamin, :no_hp, :email, :alamat)";

    foreach ($dataGuru as $index => $guru) {
        try {
            if (empty($guru['nama']) || empty($guru['nip'])) {
                 throw new Exception("Nama atau NIP tidak boleh kosong.");
            }
            
            // Cek duplikasi NIP sebelum insert
            if ($this->getGuruByNip($guru['nip'])) {
                throw new Exception("NIP '{$guru['nip']}' sudah ada di database.");
            }

            // --- PERBAIKAN LOGIKA INTI DIMULAI DI SINI ---

            // Langkah 1: Buat entri di tabel 'users'
            $hashed_password = password_hash($guru['nip'], PASSWORD_DEFAULT); // NIP sebagai password default
            $this->db->query($userQuery);
            $this->db->bind('username', $guru['nama']);
            $this->db->bind('password', $hashed_password);
            $this->db->bind('role', 'guru');
            $this->db->execute();

            $userId = $this->db->lastInsertId(); // Dapatkan ID user yang baru dibuat

            // Langkah 2: Buat entri di tabel 'guru' dengan user_id yang terhubung
            $this->db->query($guruQuery);
            $this->db->bind('user_id', $userId); // Hubungkan dengan user_id
            $this->db->bind('nama', $guru['nama']);
            $this->db->bind('nip', $guru['nip']);
            $this->db->bind('jenis_kelamin', $guru['jenis_kelamin']);
            $this->db->bind('no_hp', $guru['no_hp']);
            $this->db->bind('email', $guru['email']);
            $this->db->bind('alamat', $guru['alamat']);
            $this->db->execute();

            // --- AKHIR PERBAIKAN LOGIKA INTI ---

            if ($this->db->rowCount() > 0) {
                $berhasil++;
            } else {
                throw new Exception("Gagal menyimpan data guru karena alasan tidak diketahui.");
            }
        } catch (Exception $e) {
            $gagal++;
            $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
        }
    }

    if ($gagal > 0) {
        $this->db->rollBack();
        return ['success' => 0, 'failed' => count($dataGuru), 'errors' => $errors];
    } else {
        $this->db->commit();
        return ['success' => $berhasil, 'failed' => $gagal, 'errors' => []];
    }
}

    public function updateGuru($data) {
        $query = "UPDATE guru SET nip = :nip, nama = :nama, jenis_kelamin = :jenis_kelamin, ttl = :ttl, 
                  agama = :agama, alamat = :alamat, no_hp = :no_hp, email = :email WHERE id = :id";
        $this->db->query($query);
        $this->db->bind('id', $data['id']);
        $this->db->bind('nip', $data['nip']);
        $this->db->bind('nama', $data['nama']);
        $this->db->bind('jenis_kelamin', $data['jenis_kelamin']);
        $this->db->bind('ttl', $data['ttl']);
        $this->db->bind('agama', $data['agama']);
        $this->db->bind('alamat', $data['alamat']);
        $this->db->bind('no_hp', $data['no_hp']);
        $this->db->bind('email', $data['email']);

        $this->db->execute();
        return $this->db->rowCount();
    }

    public function hapusGuru($id) {
        $this->db->query('UPDATE kelas SET wali_kelas_id = NULL WHERE wali_kelas_id = :id');
        $this->db->bind(':id', $id);
        $this->db->execute();

        $this->db->query('DELETE FROM ' . $this->table . ' WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->execute();

        return $this->db->rowCount();
    }
    
    public function hapusGuruMassal($ids) {
        if (empty($ids)) {
            return 0;
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        // 1. Set wali_kelas_id menjadi NULL untuk kelas yang terkait
        $this->db->query("UPDATE kelas SET wali_kelas_id = NULL WHERE wali_kelas_id IN ({$placeholders})");
        foreach ($ids as $k => $id) {
            $this->db->bind($k + 1, $id);
        }
        $this->db->execute();

        // 2. Hapus guru dari tabel guru
        $this->db->query("DELETE FROM " . $this->table . " WHERE id IN ({$placeholders})"); // PERBAIKAN: Memanggil ulang query()
        foreach ($ids as $k => $id) {
            $this->db->bind($k + 1, $id);
        }
        $this->db->execute();
        
        return $this->db->rowCount();
    }

    /**
 * ==========================================================
 * METODE BARU: Untuk membuat akun user dan profil guru
 * dalam satu transaksi.
 * ==========================================================
 */
public function createGuruAndUserAccount($data) {
    $this->db->beginTransaction();

    try {
        // Langkah 1: Buat entri di tabel 'users'
        // Gunakan NIP sebagai password default (ini harus diubah oleh guru nanti)
        $hashed_password = password_hash($data['nip'], PASSWORD_DEFAULT);
        
        $this->db->query('INSERT INTO users (username, password, role) VALUES (:username, :password, :role)');
        $this->db->bind(':username', $data['nama']); // Nama guru sebagai username
        $this->db->bind(':password', $hashed_password);
        $this->db->bind(':role', 'guru'); // Role sudah pasti 'guru'
        $this->db->execute();
        
        // Ambil ID dari user yang baru saja dibuat
        $userId = $this->db->lastInsertId();

        // Langkah 2: Buat entri di tabel 'guru' dengan user_id yang terhubung
        $query = "INSERT INTO guru (user_id, nip, nama, jenis_kelamin, ttl, agama, alamat, no_hp, email) 
                  VALUES (:user_id, :nip, :nama, :jenis_kelamin, :ttl, :agama, :alamat, :no_hp, :email)";
        
        $this->db->query($query);
        $this->db->bind('user_id', $userId);
        $this->db->bind('nip', $data['nip']);
        $this->db->bind('nama', $data['nama']);
        $this->db->bind('jenis_kelamin', $data['jenis_kelamin']);
        $this->db->bind('ttl', $data['ttl']);
        $this->db->bind('agama', $data['agama']);
        $this->db->bind('alamat', $data['alamat']);
        $this->db->bind('no_hp', $data['no_hp']);
        $this->db->bind('email', $data['email']);
        
        $this->db->execute();
        
        // Jika semua berhasil, konfirmasi transaksi
        $this->db->commit();
        return $this->db->rowCount();

    } catch (Exception $e) {
        // Jika ada yang gagal, batalkan semua perubahan
        $this->db->rollBack();
        // error_log($e->getMessage()); // Untuk debugging
        return 0;
    }
}
}