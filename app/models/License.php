<?php

class License {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create($domain, $status, $requestLimit, $expiresAt, $createdBy) {
        $apiKey = $this->generateApiKey();
        $sql = "INSERT INTO licenses (domain, api_key, status, request_limit, expires_at, created_by) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $result = $this->db->query($sql, [$domain, $apiKey, $status, $requestLimit, $expiresAt, $createdBy]);
        
        if ($result) {
            return [
                'id' => $this->db->lastInsertId(),
                'api_key' => $apiKey
            ];
        }
        return false;
    }

    public function findById($id) {
        $sql = "SELECT l.*, u.username as created_by_name 
                FROM licenses l 
                LEFT JOIN users u ON l.created_by = u.id 
                WHERE l.id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt ? $stmt->fetch() : false;
    }

    public function findByApiKey($apiKey) {
        $sql = "SELECT * FROM licenses WHERE api_key = ?";
        $stmt = $this->db->query($sql, [$apiKey]);
        return $stmt ? $stmt->fetch() : false;
    }

    public function getAll($page = 1, $perPage = PER_PAGE, $search = '') {
        $offset = ($page - 1) * $perPage;
        
        if ($search) {
            $sql = "SELECT l.*, u.username as created_by_name 
                    FROM licenses l 
                    LEFT JOIN users u ON l.created_by = u.id 
                    WHERE l.domain LIKE ? OR l.api_key LIKE ?
                    ORDER BY l.created_at DESC 
                    LIMIT ? OFFSET ?";
            $searchTerm = "%$search%";
            $stmt = $this->db->query($sql, [$searchTerm, $searchTerm, $perPage, $offset]);
        } else {
            $sql = "SELECT l.*, u.username as created_by_name 
                    FROM licenses l 
                    LEFT JOIN users u ON l.created_by = u.id 
                    ORDER BY l.created_at DESC 
                    LIMIT ? OFFSET ?";
            $stmt = $this->db->query($sql, [$perPage, $offset]);
        }
        
        return $stmt ? $stmt->fetchAll() : [];
    }

    public function count($search = '') {
        if ($search) {
            $sql = "SELECT COUNT(*) as total FROM licenses WHERE domain LIKE ? OR api_key LIKE ?";
            $searchTerm = "%$search%";
            $stmt = $this->db->query($sql, [$searchTerm, $searchTerm]);
        } else {
            $sql = "SELECT COUNT(*) as total FROM licenses";
            $stmt = $this->db->query($sql);
        }
        $result = $stmt ? $stmt->fetch() : ['total' => 0];
        return $result['total'];
    }

    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }
        
        $values[] = $id;
        $sql = "UPDATE licenses SET " . implode(', ', $fields) . " WHERE id = ?";
        return $this->db->query($sql, $values);
    }

    public function delete($id) {
        // First delete related logs
        $sql = "DELETE FROM api_logs WHERE license_id = ?";
        $this->db->query($sql, [$id]);
        
        // Then delete license
        $sql = "DELETE FROM licenses WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }

    public function regenerateApiKey($id) {
        $apiKey = $this->generateApiKey();
        $sql = "UPDATE licenses SET api_key = ? WHERE id = ?";
        $result = $this->db->query($sql, [$apiKey, $id]);
        return $result ? $apiKey : false;
    }

    public function incrementRequestCount($id) {
        $sql = "UPDATE licenses SET request_count = request_count + 1 WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }

    public function resetRequestCount($id) {
        $sql = "UPDATE licenses SET request_count = 0 WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }

    public function getStatistics() {
        $stats = [];
        
        // Total licenses
        $sql = "SELECT COUNT(*) as total FROM licenses";
        $stmt = $this->db->query($sql);
        $stats['total'] = $stmt ? $stmt->fetch()['total'] : 0;
        
        // Active licenses
        $sql = "SELECT COUNT(*) as total FROM licenses WHERE status = 'active' AND (expires_at IS NULL OR expires_at > CURDATE())";
        $stmt = $this->db->query($sql);
        $stats['active'] = $stmt ? $stmt->fetch()['total'] : 0;
        
        // Expired licenses
        $sql = "SELECT COUNT(*) as total FROM licenses WHERE status = 'expired' OR expires_at <= CURDATE()";
        $stmt = $this->db->query($sql);
        $stats['expired'] = $stmt ? $stmt->fetch()['total'] : 0;
        
        // Suspended licenses
        $sql = "SELECT COUNT(*) as total FROM licenses WHERE status = 'suspended'";
        $stmt = $this->db->query($sql);
        $stats['suspended'] = $stmt ? $stmt->fetch()['total'] : 0;
        
        // Expiring soon (within 7 days)
        $sql = "SELECT COUNT(*) as total FROM licenses WHERE status = 'active' AND expires_at BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
        $stmt = $this->db->query($sql);
        $stats['expiring_soon'] = $stmt ? $stmt->fetch()['total'] : 0;
        
        return $stats;
    }

    public function getExpiringSoon($days = 7) {
        $sql = "SELECT l.*, u.username as created_by_name 
                FROM licenses l 
                LEFT JOIN users u ON l.created_by = u.id 
                WHERE l.status = 'active' 
                AND l.expires_at BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
                ORDER BY l.expires_at ASC";
        $stmt = $this->db->query($sql, [$days]);
        return $stmt ? $stmt->fetchAll() : [];
    }

    private function generateApiKey() {
        return bin2hex(random_bytes(32));
    }

    public function updateExpiredLicenses() {
        $sql = "UPDATE licenses SET status = 'expired' WHERE expires_at <= CURDATE() AND status = 'active'";
        return $this->db->query($sql);
    }
}
