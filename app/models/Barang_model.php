<?php

class Barang_model {
    private $table = 'barang';
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Ambil semua barang dengan pagination + filter
    // Ambil semua barang dengan pagination + filter
public function getBarangPaginated($offset, $limit, $filters = []) {
    $query = "SELECT * FROM {$this->table} WHERE 1=1";

    // Filter keyword (nama_barang atau kode_barang)
    if (!empty($filters['keyword'])) {
        $query .= " AND (nama_barang LIKE :keyword OR kode_barang LIKE :keyword)";
    }

    if (!empty($filters['kondisi'])) {
        $query .= " AND LOWER(kondisi) = LOWER(:kondisi)";
    }
    
    // Tambahkan filter status
    if (!empty($filters['status'])) {
        $query .= " AND status = :status";
    }

    $query .= " ORDER BY nama_barang ASC LIMIT :offset, :limit";

    $this->db->query($query);

    if (!empty($filters['keyword'])) {
        $this->db->bind(':keyword', "%" . $filters['keyword'] . "%");
    }

    if (!empty($filters['kondisi'])) {
        $this->db->bind(':kondisi', $filters['kondisi']);
    }

    // Bind parameter untuk filter status
    if (!empty($filters['status'])) {
        $this->db->bind(':status', $filters['status']);
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
    
    if (!empty($filters['kondisi'])) {
        $sql .= " AND LOWER(kondisi) = LOWER(:kondisi)";
    }
    
    // Tambahkan filter status
    if (!empty($filters['status'])) {
        $sql .= " AND status = :status";
    }

    $this->db->query($sql);

    if (!empty($filters['keyword'])) {
        $this->db->bind(':keyword', "%" . $filters['keyword'] . "%");
    }
    
    if (!empty($filters['kondisi'])) {
        $this->db->bind(':kondisi', $filters['kondisi']);
    }
    
    // Bind parameter untuk filter status
    if (!empty($filters['status'])) {
        $this->db->bind(':status', $filters['status']);
    }

    $row = $this->db->single();
    return $row['total'] ?? 0;
}

    // Update barang
public function updateBarang($data) {
    $query = "UPDATE {$this->table} 
              SET kode_barang = :kode_barang,
                  nama_barang = :nama_barang,
                  jumlah = :jumlah,
                  kondisi = :kondisi,
                  status = :status,
                  lokasi_penyimpanan = :lokasi_penyimpanan,
                  tanggal_pembelian = :tanggal_pembelian,
                  gambar = :gambar
              WHERE id = :id";
    $this->db->query($query);
    $this->db->bind(':kode_barang', $data['kode_barang']);
    $this->db->bind(':nama_barang', $data['nama_barang']);
    $this->db->bind(':jumlah', $data['jumlah']);
    $this->db->bind(':kondisi', $data['kondisi']);
    $this->db->bind(':status', $data['status']);
    $this->db->bind(':lokasi_penyimpanan', $data['lokasi_penyimpanan']);
    $this->db->bind(':tanggal_pembelian', $data['tanggal_pembelian']);
    $this->db->bind(':gambar', $data['gambar']);
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

    /**
     * ==========================================================
     * FUNGSI IMPORT BARANG BATCH (PERBAIKAN)
     * ==========================================================
     */
    public function importBarangBatch($dataBarang) {
        if (empty($dataBarang)) {
            return ['success' => 0, 'failed' => 0, 'errors' => ['Tidak ada data untuk diimpor.']];
        }

        $berhasil = 0;
        $gagal = 0;
        $errors = [];

        $this->db->beginTransaction();

        $query = "INSERT INTO {$this->table} 
                  (kode_barang, nama_barang, jumlah, kondisi, status, lokasi_penyimpanan, tanggal_pembelian, gambar) 
                  VALUES (:kode_barang, :nama_barang, :jumlah, :kondisi, :status, :lokasi_penyimpanan, :tanggal_pembelian, :gambar)";
        
        foreach ($dataBarang as $index => $barang) {
            try {
                $this->db->query($query);
                $this->db->bind(':kode_barang', $barang['kode_barang']);
                $this->db->bind(':nama_barang', $barang['nama_barang']);
                $this->db->bind(':jumlah', $barang['jumlah']);
                $this->db->bind(':kondisi', $barang['kondisi']);
                $this->db->bind(':status', $barang['status']);
                $this->db->bind(':lokasi_penyimpanan', $barang['lokasi_penyimpanan']);
                $this->db->bind(':tanggal_pembelian', $barang['tanggal_pembelian']);
                $this->db->bind(':gambar', $barang['gambar']);

                $this->db->execute();
                if ($this->db->rowCount() > 0) {
                    $berhasil++;
                } else {
                    $gagal++;
                }
            } catch (PDOException $e) {
                $gagal++;
                $errorMessage = $e->getMessage();
                // Memberikan pesan error yang lebih informatif
                if (strpos($errorMessage, 'Duplicate entry') !== false) {
                    $errors[] = "Baris " . ($index + 2) . ": Gagal menyimpan. Kemungkinan Kode Barang '{$barang['kode_barang']}' sudah ada.";
                } elseif (strpos($errorMessage, 'Data too long') !== false) {
                    $errors[] = "Baris " . ($index + 2) . ": Gagal menyimpan. Data terlalu panjang untuk salah satu kolom.";
                } elseif (strpos($errorMessage, 'Incorrect date value') !== false) {
                    $errors[] = "Baris " . ($index + 2) . ": Gagal menyimpan. Format Tanggal Pembelian tidak valid (harus YYYY-MM-DD).";
                } elseif (strpos($errorMessage, 'for key') !== false) {
                    $errors[] = "Baris " . ($index + 2) . ": Gagal menyimpan. Nilai unik duplikat. Detail: " . substr($errorMessage, 0, 100) . "...";
                } else {
                    $errors[] = "Baris " . ($index + 2) . ": Gagal menyimpan. Error tidak diketahui: " . substr($errorMessage, 0, 100) . "...";
                }
            }
        }

        if ($gagal > 0) {
            $this->db->rollBack();
        } else {
            $this->db->commit();
        }
        
        return ['success' => $berhasil, 'failed' => $gagal, 'errors' => $errors];
    }
}