<?php

class Barang_model {
    private $table = 'barang';
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Metode untuk mengambil barang dengan pagination, search, dan filter
    public function getBarangPaginated($offset, $limit, $filters = []) {
        $query = "SELECT *,
                    -- 2. Logika untuk menentukan status ketersediaan
                    CASE 
                        WHEN jumlah > 5 THEN 'Tersedia'
                        WHEN jumlah > 0 AND jumlah <= 5 THEN 'Terbatas'
                        ELSE 'Tidak Tersedia'
                    END AS status
                  FROM {$this->table} WHERE 1=1";

        // Tambahkan filter pencarian (keyword)
        if (!empty($filters['keyword'])) {
            $query .= " AND (nama_barang LIKE :keyword OR kode_barang LIKE :keyword)";
        }

        // 4. Tambahkan filter kondisi
        if (!empty($filters['kondisi'])) {
            $query .= " AND kondisi = :kondisi";
        }

        $query .= " ORDER BY id ASC LIMIT :limit OFFSET :offset";
        
        $this->db->query($query);
        
        // Binding parameter
        if (!empty($filters['keyword'])) {
            $this->db->bind(':keyword', '%' . $filters['keyword'] . '%');
        }
        if (!empty($filters['kondisi'])) {
            $this->db->bind(':kondisi', $filters['kondisi']);
        }
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        
        return $this->db->resultSet();
    }

    // Metode untuk menghitung total barang dengan filter
    public function countAllBarang($filters = []) {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";

        if (!empty($filters['keyword'])) {
            $query .= " AND (nama_barang LIKE :keyword OR kode_barang LIKE :keyword)";
        }
        if (!empty($filters['kondisi'])) {
            $query .= " AND kondisi = :kondisi";
        }

        $this->db->query($query);

        if (!empty($filters['keyword'])) {
            $this->db->bind(':keyword', '%' . $filters['keyword'] . '%');
        }
        if (!empty($filters['kondisi'])) {
            $this->db->bind(':kondisi', $filters['kondisi']);
        }

        $result = $this->db->single();
        return $result ? (int)$result['total'] : 0;
    }
    
    // --- Sisa fungsi lain (getBarangById, tambahBarang, dll) biarkan apa adanya ---
    public function getBarangById($id) {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function tambahBarang($data) {
        $query = "INSERT INTO " . $this->table . " (nama_barang, kode_barang, jumlah, kondisi, gambar, tanggal_pembelian, lokasi_penyimpanan) 
                  VALUES (:nama_barang, :kode_barang, :jumlah, :kondisi, :gambar, :tanggal_pembelian, :lokasi_penyimpanan)";

        $this->db->query($query);
        $this->db->bind('nama_barang', $data['nama_barang']);
        $this->db->bind('kode_barang', $data['kode_barang']);
        $this->db->bind('jumlah', $data['jumlah']);
        $this->db->bind('kondisi', $data['kondisi']);
        $this->db->bind('gambar', $data['gambar'] ?? null);
        $this->db->bind('tanggal_pembelian', $data['tanggal_pembelian'] ?? null);
        $this->db->bind('lokasi_penyimpanan', $data['lokasi_penyimpanan'] ?? null);

        $this->db->execute();
        return $this->db->rowCount();
    }

    // Metode untuk mengubah data barang
    public function ubahBarang($data) {
        $query = "UPDATE " . $this->table . " SET 
                    nama_barang = :nama_barang,
                    kode_barang = :kode_barang,
                    jumlah = :jumlah,
                    kondisi = :kondisi,
                    lokasi_penyimpanan = :lokasi_penyimpanan,
                    tanggal_pembelian = :tanggal_pembelian";
        
        if (!empty($data['gambar'])) {
            $query .= ", gambar = :gambar";
        }
        $query .= " WHERE id = :id";

        $this->db->query($query);
        $this->db->bind('nama_barang', $data['nama_barang']);
        $this->db->bind('kode_barang', $data['kode_barang']);
        $this->db->bind('jumlah', $data['jumlah']);
        $this->db->bind('kondisi', $data['kondisi']);
        $this->db->bind('lokasi_penyimpanan', $data['lokasi_penyimpanan']);
        $this->db->bind('tanggal_pembelian', $data['tanggal_pembelian']);
        $this->db->bind('id', $data['id']);
        
        if (!empty($data['gambar'])) {
            $this->db->bind('gambar', $data['gambar']);
        }

        $this->db->execute();
        return $this->db->rowCount();
    }

    // Metode untuk menghapus data barang
    public function hapusBarang($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $this->db->query($query);
        $this->db->bind('id', $id);

        $this->db->execute();
        return $this->db->rowCount();
    }
    

    
}