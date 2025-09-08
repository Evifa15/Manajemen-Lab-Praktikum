<?php

class Barang_model {
    private $table = 'barang';
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Ambil semua barang dengan pagination + filter
    public function getBarangPaginated($offset, $limit, $filters = []) {
        $query = "SELECT * FROM {$this->table} WHERE 1=1";

        // Filter keyword (nama_barang atau kode_barang)
        if (!empty($filters['keyword'])) {
            $query .= " AND (nama_barang LIKE :keyword OR kode_barang LIKE :keyword)";
        }

        // Filter ketersediaan (status)
        if (!empty($filters['ketersediaan'])) {
            $query .= " AND LOWER(status) = LOWER(:ketersediaan)";
        }

        $query .= " ORDER BY nama_barang ASC LIMIT :offset, :limit";

        $this->db->query($query);

        if (!empty($filters['keyword'])) {
            $this->db->bind(':keyword', "%" . $filters['keyword'] . "%");
        }

        if (!empty($filters['ketersediaan'])) {
            $this->db->bind(':ketersediaan', $filters['ketersediaan']);
        }

        $this->db->bind(':offset', (int)$offset, PDO::PARAM_INT);
        $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);

        return $this->db->resultSet();
    }

    // Hitung total barang untuk pagination + filter
    public function countAllBarang($filters = []) {
    $sql = "SELECT COUNT(*) as total FROM barang WHERE 1=1";

    if (!empty($filters['keyword'])) {
        $sql .= " AND nama_barang LIKE :keyword";
    }
    if (!empty($filters['ketersediaan'])) {
        $sql .= " AND LOWER(status) = LOWER(:ketersediaan)";
    }

    $this->db->query($sql);

    if (!empty($filters['keyword'])) {
        $this->db->bind(':keyword', "%" . $filters['keyword'] . "%");
    }
    if (!empty($filters['ketersediaan'])) {
        $this->db->bind(':ketersediaan', $filters['ketersediaan']);
    }

    $row = $this->db->single();
    return $row['total'] ?? 0;
}


    // Ambil barang berdasarkan ID
    public function getBarangById($id) {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Tambah barang baru
    public function tambahBarang($data) {
        $query = "INSERT INTO {$this->table} 
                  (kode_barang, nama_barang, jumlah, gambar, status) 
                  VALUES (:kode_barang, :nama_barang, :jumlah, :gambar, :status)";
        $this->db->query($query);
        $this->db->bind(':kode_barang', $data['kode_barang']);
        $this->db->bind(':nama_barang', $data['nama_barang']);
        $this->db->bind(':jumlah', $data['jumlah']);
        $this->db->bind(':gambar', $data['gambar']);
        $this->db->bind(':status', $data['status']);
        $this->db->execute();
        return $this->db->rowCount();
    }

    // Update barang
    public function updateBarang($data) {
        $query = "UPDATE {$this->table} 
                  SET kode_barang = :kode_barang,
                      nama_barang = :nama_barang,
                      jumlah = :jumlah,
                      gambar = :gambar,
                      status = :status
                  WHERE id = :id";
        $this->db->query($query);
        $this->db->bind(':kode_barang', $data['kode_barang']);
        $this->db->bind(':nama_barang', $data['nama_barang']);
        $this->db->bind(':jumlah', $data['jumlah']);
        $this->db->bind(':gambar', $data['gambar']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':id', $data['id']);
        $this->db->execute();
        return $this->db->rowCount();
    }

    // Hapus barang
    public function deleteBarang($id) {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->execute();
        return $this->db->rowCount();
    }

    // Kurangi stok barang
    public function kurangiStok($id, $jumlah) {
        $query = "UPDATE {$this->table} SET jumlah = jumlah - :jumlah WHERE id = :id AND jumlah >= :jumlah";
        $this->db->query($query);
        $this->db->bind(':jumlah', $jumlah);
        $this->db->bind(':id', $id);
        $this->db->execute();
        return $this->db->rowCount();
    }

    // Tambah stok barang
    public function tambahStok($id, $jumlah) {
        $query = "UPDATE {$this->table} SET jumlah = jumlah + :jumlah WHERE id = :id";
        $this->db->query($query);
        $this->db->bind(':jumlah', $jumlah);
        $this->db->bind(':id', $id);
        $this->db->execute();
        return $this->db->rowCount();
    }

    // Ambil beberapa barang berdasarkan array ID
    public function getBarangByIds($ids) {
        if (empty($ids)) {
            return [];
        }
        // Membuat placeholder sebanyak jumlah id
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        
        $query = "SELECT * FROM {$this->table} WHERE id IN ({$placeholders})";
        
        $this->db->query($query);
        
        // Binding setiap id ke placeholder
        foreach ($ids as $k => $id) {
            $this->db->bind($k + 1, $id);
        }

        return $this->db->resultSet();
    }
    public function hapusDariKeranjang($id) {
        $this->checkAuth();
        if (isset($_SESSION['keranjang'])) {
            // Cari key dari item yang akan dihapus
            $key = array_search($id, $_SESSION['keranjang']);
            if ($key !== false) {
                // Hapus item dari array
                unset($_SESSION['keranjang'][$key]);
                Flasher::setFlash('Berhasil!', 'Item dihapus dari keranjang.', 'success');
            }
        }
        header('Location: ' . BASEURL . '/siswa/katalog');
        exit;
    }
}
