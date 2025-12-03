<?php

require_once __DIR__ . '/../util/Db.php';

class SponsorModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Db::getInstance();
    }

    /**
     * 获取所有启用的赞助商列表（按显示顺序排序）
     */
    public function getList(): array
    {
        $sql = 'SELECT sponsor_id, sponsor_name, logo_url, display_order
                FROM cmx_sponsors
                WHERE status = 1
                ORDER BY display_order ASC, sponsor_id ASC';
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * 获取所有赞助商（包括隐藏的，用于后台管理）
     */
    public function getAll(): array
    {
        $sql = 'SELECT * FROM cmx_sponsors ORDER BY display_order ASC, sponsor_id ASC';
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * 根据ID获取赞助商
     */
    public function getById(int $sponsorId): ?array
    {
        $sql = 'SELECT * FROM cmx_sponsors WHERE sponsor_id = :sponsor_id LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':sponsor_id', $sponsorId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * 添加赞助商
     */
    public function add(string $sponsorName, string $logoUrl, int $displayOrder = 0, int $status = 1): int
    {
        $sql = 'INSERT INTO cmx_sponsors (sponsor_name, logo_url, display_order, status)
                VALUES (:sponsor_name, :logo_url, :display_order, :status)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':sponsor_name', $sponsorName, PDO::PARAM_STR);
        $stmt->bindValue(':logo_url', $logoUrl, PDO::PARAM_STR);
        $stmt->bindValue(':display_order', $displayOrder, PDO::PARAM_INT);
        $stmt->bindValue(':status', $status, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$this->db->lastInsertId();
    }

    /**
     * 更新赞助商
     */
    public function update(int $sponsorId, string $sponsorName, string $logoUrl, int $displayOrder, int $status): bool
    {
        $sql = 'UPDATE cmx_sponsors 
                SET sponsor_name = :sponsor_name, logo_url = :logo_url, 
                    display_order = :display_order, status = :status
                WHERE sponsor_id = :sponsor_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':sponsor_id', $sponsorId, PDO::PARAM_INT);
        $stmt->bindValue(':sponsor_name', $sponsorName, PDO::PARAM_STR);
        $stmt->bindValue(':logo_url', $logoUrl, PDO::PARAM_STR);
        $stmt->bindValue(':display_order', $displayOrder, PDO::PARAM_INT);
        $stmt->bindValue(':status', $status, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * 删除赞助商
     */
    public function delete(int $sponsorId): bool
    {
        $sql = 'DELETE FROM cmx_sponsors WHERE sponsor_id = :sponsor_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':sponsor_id', $sponsorId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

