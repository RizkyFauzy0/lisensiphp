<?php

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create($username, $email, $password, $role = 'admin') {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
        $result = $this->db->query($sql, [$username, $email, $hashedPassword, $role]);
        return $result ? $this->db->lastInsertId() : false;
    }

    public function findByUsername($username) {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->db->query($sql, [$username]);
        return $stmt ? $stmt->fetch() : false;
    }

    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->db->query($sql, [$email]);
        return $stmt ? $stmt->fetch() : false;
    }

    public function findById($id) {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt ? $stmt->fetch() : false;
    }

    public function getAll($page = 1, $perPage = PER_PAGE) {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = $this->db->query($sql, [$perPage, $offset]);
        return $stmt ? $stmt->fetchAll() : [];
    }

    public function count() {
        $sql = "SELECT COUNT(*) as total FROM users";
        $stmt = $this->db->query($sql);
        $result = $stmt ? $stmt->fetch() : ['total' => 0];
        return $result['total'];
    }

    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            if ($key === 'password' && !empty($value)) {
                $fields[] = "password = ?";
                $values[] = password_hash($value, PASSWORD_BCRYPT);
            } elseif ($key !== 'password') {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }
        
        $values[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        return $this->db->query($sql, $values);
    }

    public function delete($id) {
        $sql = "DELETE FROM users WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }

    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
}
