<?php

class Siswa_model {
    private $table = 'siswa';
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getSiswaByKelasIdPaginated($kelas_id, $offset, $limit, $keyword = null) {
        $sql = 'SELECT id, id_siswa, nama, jenis_kelamin, status FROM ' . $this->table . ' WHERE kelas_id = :kelas_id';
        if (!empty($keyword)) {
            $sql .= ' AND (nama LIKE :keyword OR id_siswa LIKE :keyword)';
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
        $sql = 'SELECT COUNT(*) as total FROM ' . $this->table . ' WHERE kelas_id = :kelas_id';
        if (!empty($keyword)) {
            $sql .= ' AND (nama LIKE :keyword OR id_siswa LIKE :keyword)';
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
}