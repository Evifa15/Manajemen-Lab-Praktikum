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
    $query = "SELECT *, 
                CASE
                    WHEN jumlah >= 4 THEN 'Tersedia'
                    WHEN jumlah >= 1 AND jumlah <= 3 THEN 'Terbatas'
                    ELSE 'Tidak Tersedia'
                END AS status_barang
              FROM {$this->table} WHERE 1=1";

    if (!empty($filters['keyword'])) {
        $query .= " AND (nama_barang LIKE :keyword OR kode_barang LIKE :keyword)";
    }

    if (!empty($filters['status'])) {
        $query .= " HAVING status_barang = :status";
    }

    $query .= " ORDER BY nama_barang ASC LIMIT :offset, :limit";

    $this->db->query($query);

    if (!empty($filters['keyword'])) {
        $this->db->bind(':keyword', "%" . $filters['keyword'] . "%");
    }

    if (!empty($filters['status'])) {
        $this->db->bind(':status', $filters['status']);
    }

    $this->db->bind(':offset', (int)$offset, PDO::PARAM_INT);
    $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);

    return $this->db->resultSet();
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
    /**
     * Mengambil satu data barang berdasarkan ID-nya.
     */
    public function getBarangById($id) {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id");
        $this->db->bind('id', $id);
        return $this->db->single();
    }
    /**
 * Menambahkan data barang baru ke database.
 */
public function tambahBarang($data) {
    $query = "INSERT INTO {$this->table} 
              (kode_barang, nama_barang, jumlah, kondisi, status, lokasi_penyimpanan, tanggal_pembelian, gambar) 
              VALUES (:kode_barang, :nama_barang, :jumlah, :kondisi, :status, :lokasi_penyimpanan, :tanggal_pembelian, :gambar)";

    $this->db->query($query);
    $this->db->bind(':kode_barang', $data['kode_barang']);
    $this->db->bind(':nama_barang', $data['nama_barang']);
    $this->db->bind(':jumlah', $data['jumlah']);
    $this->db->bind(':kondisi', $data['kondisi']);
    $this->db->bind(':status', $data['status']);
    $this->db->bind(':lokasi_penyimpanan', $data['lokasi_penyimpanan']);
    $this->db->bind(':tanggal_pembelian', $data['tanggal_pembelian']);
    $this->db->bind(':gambar', $data['gambar']);

    $this->db->execute();
    return $this->db->rowCount();
}
/**
 * Mengambil satu data barang berdasarkan kode barangnya.
 */
public function getBarangByKode($kode) {
    $this->db->query("SELECT * FROM {$this->table} WHERE kode_barang = :kode_barang");
    $this->db->bind(':kode_barang', $kode);
    return $this->db->single();
}
/**
 * Hitung total barang untuk pagination + filter
 */
/**
 * Hitung total barang untuk pagination + filter
 */
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
/**
     * Mengambil data barang berdasarkan array ID.
     * Digunakan untuk menampilkan item di keranjang.
     */
    public function getBarangByIds($ids) {
        if (empty($ids)) {
            return [];
        }
        
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        
        $query = "SELECT *, 
                    CASE
                        WHEN jumlah >= 4 THEN 'Tersedia'
                        WHEN jumlah >= 1 AND jumlah <= 3 THEN 'Terbatas'
                        ELSE 'Tidak Tersedia'
                    END AS status
                  FROM {$this->table} WHERE id IN ({$placeholders}) ORDER BY nama_barang ASC";
        
        $this->db->query($query);
        
        foreach ($ids as $key => $id) {
            $this->db->bind($key + 1, $id, PDO::PARAM_INT);
        }

        return $this->db->resultSet();
    }
    /**
     * Memperbarui data barang di database.
     */
    public function updateBarang($data) {
        // Logika status otomatis yang kita buat sebelumnya
        $jumlah = (int)$data['jumlah'];
        if ($jumlah <= 0) {
            $data['status'] = 'Tidak Tersedia';
        } elseif ($jumlah >= 1 && $jumlah <= 3) {
            $data['status'] = 'Terbatas';
        } else {
            $data['status'] = 'Tersedia';
        }
    
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
}