<?php

class Peminjaman_model {
    private $table = 'peminjaman';
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getHistoryPaginated($offset, $limit, $filters = []) {
        // ✅ PERBAIKAN: Mengganti u.id_pengguna dengan statement CASE dan JOIN
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
    
    public function countAllHistory($filters = []) {
        // ✅ PERBAIKAN: Mengganti u.id_pengguna dengan statement CASE dan JOIN
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
     * ✅ FUNGSI BARU: Mengambil semua data riwayat untuk di-ekspor ke CSV.
     * Tidak ada LIMIT/OFFSET di sini.
     */
    public function getAllHistoryForExport($filters = []) {
        // ✅ PERBAIKAN: Mengganti u.id_pengguna dengan statement CASE dan JOIN
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
}