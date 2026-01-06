<?php

class ApiLog {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create($licenseId, $ipAddress, $requestDomain, $status, $message = '') {
        $sql = "INSERT INTO api_logs (license_id, ip_address, request_domain, status, message) 
                VALUES (?, ?, ?, ?, ?)";
        return $this->db->query($sql, [$licenseId, $ipAddress, $requestDomain, $status, $message]);
    }

    public function getByLicenseId($licenseId, $page = 1, $perPage = PER_PAGE) {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT * FROM api_logs WHERE license_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = $this->db->query($sql, [$licenseId, $perPage, $offset]);
        return $stmt ? $stmt->fetchAll() : [];
    }

    public function countByLicenseId($licenseId) {
        $sql = "SELECT COUNT(*) as total FROM api_logs WHERE license_id = ?";
        $stmt = $this->db->query($sql, [$licenseId]);
        $result = $stmt ? $stmt->fetch() : ['total' => 0];
        return $result['total'];
    }

    public function getRecent($limit = 10) {
        $sql = "SELECT al.*, l.domain 
                FROM api_logs al 
                LEFT JOIN licenses l ON al.license_id = l.id 
                ORDER BY al.created_at DESC 
                LIMIT ?";
        $stmt = $this->db->query($sql, [$limit]);
        return $stmt ? $stmt->fetchAll() : [];
    }

    public function getStatsByLicense($licenseId, $days = 30) {
        $sql = "SELECT 
                    DATE(created_at) as date,
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as success,
                    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
                    SUM(CASE WHEN status = 'blocked' THEN 1 ELSE 0 END) as blocked
                FROM api_logs 
                WHERE license_id = ? 
                AND created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                GROUP BY DATE(created_at)
                ORDER BY date DESC";
        $stmt = $this->db->query($sql, [$licenseId, $days]);
        return $stmt ? $stmt->fetchAll() : [];
    }

    public function getOverallStats($days = 30) {
        $sql = "SELECT 
                    DATE(created_at) as date,
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as success,
                    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
                    SUM(CASE WHEN status = 'blocked' THEN 1 ELSE 0 END) as blocked
                FROM api_logs 
                WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                GROUP BY DATE(created_at)
                ORDER BY date DESC";
        $stmt = $this->db->query($sql, [$days]);
        return $stmt ? $stmt->fetchAll() : [];
    }

    public function deleteOldLogs($days = 90) {
        $sql = "DELETE FROM api_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)";
        return $this->db->query($sql, [$days]);
    }
}
