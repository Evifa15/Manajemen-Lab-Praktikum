<?php

class Peminjaman_model {
    private $table = 'peminjaman';
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    /**
     * Mengambil satu data peminjaman berdasarkan ID-nya.
     */
    public function getPeminjamanById($id) {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id");
        $this->db->bind('id', $id);
        return $this->db->single();
    }

    /**
     * Memperbarui status peminjaman (misal: 'Disetujui', 'Ditolak', 'Dikembalikan').
     */
    public function updateStatusPeminjaman($id, $status) {
        $query = "UPDATE {$this->table} SET status = :status WHERE id = :id";
        $this->db->query($query);
        $this->db->bind('id', $id);
        $this->db->bind('status', $status);
        $this->db->execute();
        return $this->db->rowCount();
    }

    /**
     * Membuat beberapa record peminjaman sekaligus dalam satu transaksi yang aman.
     * Logika pengurangan stok juga dimasukkan di sini untuk menjamin integritas data.
     */
    public function createPeminjamanBatch($dataPeminjaman) {
    if (empty($dataPeminjaman)) {
        return 0;
    }

    $this->db->beginTransaction();

    try {
        $peminjamanQuery = "INSERT INTO " . $this->table . " (user_id, barang_id, jumlah_pinjam, tanggal_pinjam, tanggal_wajib_kembali, status, keperluan, verifikator_id) VALUES (:user_id, :barang_id, :jumlah_pinjam, :tanggal_pinjam, :tanggal_wajib_kembali, :status, :keperluan, :verifikator_id)";
        $stokQuery = "UPDATE barang SET jumlah = jumlah - :jumlah WHERE id = :barang_id AND jumlah >= :jumlah";

        foreach ($dataPeminjaman as $peminjaman) {
            // 1. Kurangi stok barang
            $this->db->query($stokQuery);
            $this->db->bind('barang_id', $peminjaman['barang_id']);
            $this->db->bind('jumlah', $peminjaman['jumlah_pinjam']);
            $this->db->execute();
            if ($this->db->rowCount() == 0) {
                throw new Exception("Stok untuk barang ID {$peminjaman['barang_id']} habis atau tidak ditemukan.");
            }

            // 2. Simpan data peminjaman
            $this->db->query($peminjamanQuery);
            $this->db->bind('user_id', $peminjaman['user_id']);
            $this->db->bind('barang_id', $peminjaman['barang_id']);
            $this->db->bind('jumlah_pinjam', $peminjaman['jumlah_pinjam']);
            $this->db->bind('tanggal_pinjam', $peminjaman['tanggal_pinjam']);
            $this->db->bind('tanggal_wajib_kembali', $peminjaman['tanggal_kembali_diajukan']);
            $this->db->bind('status', 'Menunggu Verifikasi');
            $this->db->bind('keperluan', $peminjaman['keperluan']);
            $this->db->bind('verifikator_id', $peminjaman['verifikator_id']);
            $this->db->execute();
        }

        $this->db->commit();
        return count($dataPeminjaman);
    } catch (Exception $e) {
        $this->db->rollBack();
        return 0;
    }
}

    /**
     * Mengambil data peminjaman yang perlu diverifikasi oleh guru spesifik.
     */
    public function getPeminjamanForVerification($verifikator_id, $offset, $limit, $keyword = null) {
    $query = "SELECT p.*, s.nama as nama_siswa, s.id_siswa, b.nama_barang, b.kode_barang, b.jumlah as stok_barang 
              FROM " . $this->table . " p 
              JOIN siswa s ON p.user_id = s.user_id 
              JOIN barang b ON p.barang_id = b.id
              WHERE p.verifikator_id = :verifikator_id AND p.status = 'Menunggu Verifikasi'";

    // Tambahkan pencarian jika ada keyword
    if (!empty($keyword)) {
        $query .= " AND (s.nama LIKE :keyword OR s.id_siswa LIKE :keyword OR b.nama_barang LIKE :keyword)";
    }

    $query .= " ORDER BY p.tanggal_pinjam ASC LIMIT :offset, :limit";

    $this->db->query($query);
    $this->db->bind('verifikator_id', $verifikator_id);

    if (!empty($keyword)) {
        $this->db->bind(':keyword', "%" . $keyword . "%");
    }

    $this->db->bind('offset', (int)$offset, PDO::PARAM_INT);
    $this->db->bind('limit', (int)$limit, PDO::PARAM_INT);

    return $this->db->resultSet();
}
    
    /**
     * Mengambil riwayat peminjaman untuk satu siswa (dengan paginasi).
     */
   public function getHistoryByUserId($userId, $offset, $limit, $keyword = null) {
    $query = "SELECT p.*, b.nama_barang, b.kode_barang 
              FROM {$this->table} p 
              JOIN barang b ON p.barang_id = b.id 
              WHERE p.user_id = :user_id";

    if (!empty($keyword)) {
        $query .= " AND (b.nama_barang LIKE :keyword OR b.kode_barang LIKE :keyword OR p.keperluan LIKE :keyword)";
    }

    $query .= " ORDER BY p.tanggal_pinjam DESC 
                LIMIT :offset, :limit";

    $this->db->query($query);
    $this->db->bind('user_id', $userId);
    $this->db->bind('offset', (int)$offset, PDO::PARAM_INT);
    $this->db->bind('limit', (int)$limit, PDO::PARAM_INT);

    if (!empty($keyword)) {
        $this->db->bind(':keyword', "%" . $keyword . "%");
    }

    return $this->db->resultSet();
}
    
    /**
     * Menghitung total riwayat peminjaman untuk satu siswa.
     */
    public function countHistoryByUserId($userId, $keyword = null) {
    $query = "SELECT COUNT(p.id) as total 
              FROM {$this->table} p 
              JOIN barang b ON p.barang_id = b.id 
              WHERE p.user_id = :user_id";

    if (!empty($keyword)) {
        $query .= " AND (b.nama_barang LIKE :keyword OR b.kode_barang LIKE :keyword OR p.keperluan LIKE :keyword)";
    }

    $this->db->query($query);
    $this->db->bind('user_id', $userId);

    if (!empty($keyword)) {
        $this->db->bind(':keyword', "%" . $keyword . "%");
    }

    $result = $this->db->single();
    return $result['total'] ?? 0;
}

    /**
     * Mengambil semua riwayat peminjaman (untuk Laporan Admin).
     */
    public function getHistoryPaginated($offset, $limit, $filters = []) {
        $sql = "SELECT 
                    p.id, 
                    u.username as nama_peminjam,
                    CASE
                        WHEN u.role = 'admin' THEN a.id_admin
                        WHEN u.role = 'guru' THEN g.nip
                        WHEN u.role = 'siswa' THEN s.id_siswa
                    END AS no_id_peminjam,
                    b.nama_barang, 
                    p.tanggal_pinjam, 
                    p.tanggal_kembali, 
                    p.status, 
                    p.bukti_kembali
                FROM {$this->table} p
                JOIN users u ON p.user_id = u.id
                JOIN barang b ON p.barang_id = b.id
                LEFT JOIN admin a ON u.id = a.user_id
                LEFT JOIN guru g ON u.id = g.user_id
                LEFT JOIN siswa s ON u.id = s.user_id
                WHERE 1=1";

        if (!empty($filters['keyword'])) {
            $sql .= " AND (u.username LIKE :keyword OR 
                            (CASE
                                WHEN u.role = 'admin' THEN a.id_admin
                                WHEN u.role = 'guru' THEN g.nip
                                WHEN u.role = 'siswa' THEN s.id_siswa
                            END) LIKE :keyword 
                            OR b.nama_barang LIKE :keyword)";
        }
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $sql .= " AND p.tanggal_pinjam BETWEEN :start_date AND :end_date";
        }

        $sql .= " ORDER BY p.tanggal_pinjam DESC LIMIT :limit OFFSET :offset";
        
        $this->db->query($sql);

        if (!empty($filters['keyword'])) {
            $this->db->bind(':keyword', '%' . $filters['keyword'] . '%');
        }
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $this->db->bind(':start_date', $filters['start_date']);
            $this->db->bind(':end_date', $filters['end_date']);
        }
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);

        return $this->db->resultSet();
    }
    
    /**
     * Menghitung semua riwayat peminjaman (untuk Laporan Admin).
     */
    public function countAllHistory($filters = []) {
        $sql = "SELECT COUNT(p.id) as total
                FROM {$this->table} p
                JOIN users u ON p.user_id = u.id
                JOIN barang b ON p.barang_id = b.id
                LEFT JOIN admin a ON u.id = a.user_id
                LEFT JOIN guru g ON u.id = g.user_id
                LEFT JOIN siswa s ON u.id = s.user_id
                WHERE 1=1";

        if (!empty($filters['keyword'])) {
            $sql .= " AND (u.username LIKE :keyword OR 
                            (CASE
                                WHEN u.role = 'admin' THEN a.id_admin
                                WHEN u.role = 'guru' THEN g.nip
                                WHEN u.role = 'siswa' THEN s.id_siswa
                            END) LIKE :keyword 
                            OR b.nama_barang LIKE :keyword)";
        }
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $sql .= " AND p.tanggal_pinjam BETWEEN :start_date AND :end_date";
        }

        $this->db->query($sql);
        
        if (!empty($filters['keyword'])) {
            $this->db->bind(':keyword', '%' . $filters['keyword'] . '%');
        }
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $this->db->bind(':start_date', $filters['start_date']);
            $this->db->bind(':end_date', $filters['end_date']);
        }

        $result = $this->db->single();
        return $result ? (int)$result['total'] : 0;
    }

    /**
     * Mengambil semua riwayat untuk diekspor ke CSV.
     */
    public function getAllHistoryForExport($filters = []) {
        $sql = "SELECT 
                    p.id, 
                    u.username as nama_peminjam, 
                    CASE
                        WHEN u.role = 'admin' THEN a.id_admin
                        WHEN u.role = 'guru' THEN g.nip
                        WHEN u.role = 'siswa' THEN s.id_siswa
                    END AS no_id_peminjam,
                    b.nama_barang, 
                    p.tanggal_pinjam, 
                    p.tanggal_kembali, 
                    p.status
                FROM {$this->table} p
                JOIN users u ON p.user_id = u.id
                JOIN barang b ON p.barang_id = b.id
                LEFT JOIN admin a ON u.id = a.user_id
                LEFT JOIN guru g ON u.id = g.user_id
                LEFT JOIN siswa s ON u.id = s.user_id
                WHERE 1=1";

        if (!empty($filters['keyword'])) {
            $sql .= " AND (u.username LIKE :keyword OR 
                            (CASE
                                WHEN u.role = 'admin' THEN a.id_admin
                                WHEN u.role = 'guru' THEN g.nip
                                WHEN u.role = 'siswa' THEN s.id_siswa
                            END) LIKE :keyword 
                            OR b.nama_barang LIKE :keyword)";
        }
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $sql .= " AND p.tanggal_pinjam BETWEEN :start_date AND :end_date";
        }

        $sql .= " ORDER BY p.tanggal_pinjam DESC";
        
        $this->db->query($sql);

        if (!empty($filters['keyword'])) {
            $this->db->bind(':keyword', '%' . $filters['keyword'] . '%');
        }
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $this->db->bind(':start_date', $filters['start_date']);
            $this->db->bind(':end_date', $filters['end_date']);
        }

        return $this->db->resultSet();
    }

    public function countAllVerificationRequests($verifikator_id, $keyword = null) {
    $query = "SELECT COUNT(p.id) as total 
              FROM " . $this->table . " p 
              JOIN siswa s ON p.user_id = s.user_id 
              JOIN barang b ON p.barang_id = b.id
              WHERE p.verifikator_id = :verifikator_id AND p.status = 'Menunggu Verifikasi'";

    // Tambahkan pencarian jika ada keyword
    if (!empty($keyword)) {
        $query .= " AND (s.nama LIKE :keyword OR s.id_siswa LIKE :keyword OR b.nama_barang LIKE :keyword)";
    }

    $this->db->query($query);
    $this->db->bind('verifikator_id', $verifikator_id);

    if (!empty($keyword)) {
        $this->db->bind(':keyword', "%" . $keyword . "%");
    }

    $result = $this->db->single();
    return $result ? (int)$result['total'] : 0;
}
/**
     * Memperbarui status dan keterangan peminjaman.
     */
    public function updatePeminjamanStatusAndKeterangan($id, $status, $keterangan = null) {
        $query = "UPDATE {$this->table} SET status = :status, keterangan = :keterangan WHERE id = :id";
        $this->db->query($query);
        $this->db->bind('id', $id);
        $this->db->bind('status', $status);
        $this->db->bind('keterangan', $keterangan);
        $this->db->execute();
        return $this->db->rowCount();
    }
}

