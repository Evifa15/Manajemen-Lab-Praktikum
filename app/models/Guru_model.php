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
        // ✅ PERBAIKAN: Tambahkan 'jenis_kelamin' dan 'no_hp' ke kondisi pencarian
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
        // ✅ PERBAIKAN: Tambahkan 'jenis_kelamin' dan 'no_hp' ke kondisi pencarian
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

    // Metode lainnya (getGuruById, tambahGuru, dll) tetap sama...
    public function getGuruById($id) {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE id = :id');
        $this->db->bind(':id', $id);
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
}