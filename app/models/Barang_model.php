<?php

class Barang_model {
    private $table = 'barang';
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Metode untuk mengambil semua data barang
    public function getAllBarang() {
        $this->db->query('SELECT * FROM ' . $this->table);
        return $this->db->resultSet();
    }

    // Metode untuk mengambil data barang berdasarkan ID
    public function getBarangById($id) {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Metode untuk menambah data barang baru
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
    
    // Metode untuk menghitung total barang
    public function countAllBarang() {
        $this->db->query('SELECT COUNT(*) as total FROM ' . $this->table);
        $result = $this->db->single();
        return $result ? (int)$result['total'] : 0;
    }

    // Metode untuk mengambil barang dengan pagination
    public function getBarangPaginated($offset, $limit) {
        // ✅ PERBAIKAN: Mengubah urutan LIMIT dan OFFSET
        $this->db->query('SELECT * FROM ' . $this->table . ' LIMIT :limit OFFSET :offset');
        $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
        $this->db->bind(':offset', (int)$offset, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
}