<?php

class Kelas_model {
    private $table = 'kelas';
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getKelasPaginated($offset, $limit, $keyword = null) {
        $sql = 'SELECT k.id, k.nama_kelas, k.wali_kelas_id, g.nama as nama_wali_kelas 
                FROM ' . $this->table . ' k
                LEFT JOIN guru g ON k.wali_kelas_id = g.id';
        
        if (!empty($keyword)) {
            $sql .= ' WHERE k.nama_kelas LIKE :keyword OR g.nama LIKE :keyword';
        }
        
        $sql .= ' ORDER BY k.nama_kelas ASC LIMIT :limit OFFSET :offset';

        $this->db->query($sql);

        if (!empty($keyword)) {
            $this->db->bind(':keyword', '%' . $keyword . '%');
        }
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function countAllKelas($keyword = null) {
        $sql = 'SELECT COUNT(k.id) as total 
                FROM ' . $this->table . ' k
                LEFT JOIN guru g ON k.wali_kelas_id = g.id';
        
        if (!empty($keyword)) {
            $sql .= ' WHERE k.nama_kelas LIKE :keyword OR g.nama LIKE :keyword';
        }

        $this->db->query($sql);

        if (!empty($keyword)) {
            $this->db->bind(':keyword', '%' . $keyword . '%');
        }
        
        $result = $this->db->single();
        return $result ? (int)$result['total'] : 0;
    }
    
    // Metode lainnya (getKelasById, tambahKelas, dll) tetap sama...
    public function getKelasById($id) {
        // ✅ PERBAIKAN: JOIN dengan tabel guru untuk mendapat info wali kelas
        $this->db->query(
            'SELECT k.*, g.nama as nama_wali_kelas, g.nip 
             FROM ' . $this->table . ' k
             LEFT JOIN guru g ON k.wali_kelas_id = g.id
             WHERE k.id = :id'
        );
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function tambahKelas($data) {
        $query = "INSERT INTO " . $this->table . " (nama_kelas, wali_kelas_id) VALUES (:nama_kelas, :wali_kelas_id)";
        $this->db->query($query);
        $this->db->bind('nama_kelas', $data['nama_kelas']);
        $this->db->bind('wali_kelas_id', $data['wali_kelas_id']);

        $this->db->execute();
        return $this->db->rowCount();
    }

    public function updateKelas($data) {
        $query = "UPDATE " . $this->table . " SET nama_kelas = :nama_kelas, wali_kelas_id = :wali_kelas_id WHERE id = :id";
        $this->db->query($query);
        $this->db->bind('id', $data['id']);
        $this->db->bind('nama_kelas', $data['nama_kelas']);
        $this->db->bind('wali_kelas_id', $data['wali_kelas_id']);

        $this->db->execute();
        return $this->db->rowCount();
    }

    public function hapusKelas($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $this->db->query($query);
        $this->db->bind('id', $id);

        $this->db->execute();
        return $this->db->rowCount();
    }
}
