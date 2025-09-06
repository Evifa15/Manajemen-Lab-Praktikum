<?php

class User_model {
    private $table = 'users';
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function findUserByUsernameAndRole($username, $role) {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE username = :username AND role = :role');
        $this->db->bind(':username', $username);
        $this->db->bind(':role', $role);
        return $this->db->single();
    }
    
    // ✅ PERBAIKAN: Mengembalikan kolom 'email'
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
    
    public function getUserById($id) {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    // ✅ PERBAIKAN: Mengembalikan kolom 'email'
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

    // ✅ PERBAIKAN: Mengembalikan kolom 'email'
    public function getUsersPaginated($offset, $limit) {
        $this->db->query('SELECT id, id_pengguna, username, email, role FROM ' . $this->table . ' LIMIT :limit OFFSET :offset');
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
    
    public function countAllUsers() {
        $this->db->query('SELECT COUNT(*) AS total FROM ' . $this->table);
        $result = $this->db->single();
        return $result ? (int)$result['total'] : 0;
    }
}

