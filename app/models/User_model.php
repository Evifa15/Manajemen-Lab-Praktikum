<?php

class User_model {
    private $table = 'users';
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    /**
     * Mencari user berdasarkan username dan role.
     * @param string $username
     * @param string $role
     * @return mixed Array data user jika ditemukan, false jika tidak.
     */
    public function findUserByUsernameAndRole($username, $role) {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE username = :username AND role = :role');
        $this->db->bind(':username', $username);
        $this->db->bind(':role', $role);
        
        return $this->db->single();
    }
    
    public function getAllUsers() {
        $this->db->query('SELECT * FROM ' . $this->table);
        return $this->db->resultSet();
    }
    
    public function tambahUser($data) {
        $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $this->db->query('INSERT INTO ' . $this->table . ' (username, id_pengguna, password, email, role) VALUES (:username, :id_pengguna, :password, :email, :role)');
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':id_pengguna', $data['id_pengguna']);
        $this->db->bind(':password', $hashed_password);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':role', $data['role']);

        $this->db->execute();
        return $this->db->rowCount();
    }
    
    public function findUserByUsername($username) {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE username = :username');
        $this->db->bind(':username', $username);
        
        return $this->db->single();
    }
    
    public function hapusUser($id) {
        $this->db->query('DELETE FROM ' . $this->table . ' WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->execute();

        return $this->db->rowCount();
    }
    
    // --- Metode BARU: Mengambil data pengguna berdasarkan ID ---
    public function getUserById($id) {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    // --- Metode BARU: Memperbarui data pengguna ---
    public function updateUser($data) {
        if (empty($data['password'])) {
            $query = 'UPDATE ' . $this->table . ' SET username = :username, id_pengguna = :id_pengguna, email = :email, role = :role WHERE id = :id';
        } else {
            $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
            $query = 'UPDATE ' . $this->table . ' SET username = :username, id_pengguna = :id_pengguna, password = :password, email = :email, role = :role WHERE id = :id';
        }

        $this->db->query($query);
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':id_pengguna', $data['id_pengguna']);
        if (!empty($data['password'])) {
            $this->db->bind(':password', $hashed_password);
        }
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':role', $data['role']);

        $this->db->execute();
        return $this->db->rowCount();
    }

    // --- Metode BARU untuk Pagination ---
    /**
     * Mengambil user untuk halaman tertentu.
     * @param int $offset Offset (mulai dari baris ke berapa).
     * @param int $limit Jumlah baris per halaman.
     * @return array Array data user.
     */
    public function getUsersPaginated($offset, $limit) {
        $this->db->query('SELECT * FROM ' . $this->table . ' LIMIT :limit OFFSET :offset');
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
    
    /**
     * Menghitung total jumlah user.
     * @return int Total jumlah user.
     */
    public function countAllUsers() {
        $this->db->query('SELECT COUNT(*) AS total FROM ' . $this->table);
        return $this->db->single()['total'];
    }
    // --- Akhir Metode BARU ---
}