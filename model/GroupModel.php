<?php

require_once __DIR__ . '/../util/Db.php';

class GroupModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Db::getInstance();
    }

    /**
     * 获取所有分组列表
     */
    public function getList(): array
    {
        $sql = 'SELECT group_id, group_name, group_code, status FROM cmx_groups ORDER BY group_id ASC';
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * 根据 ID 获取分组信息
     */
    public function getById(int $groupId): ?array
    {
        $sql = 'SELECT group_id, group_name, group_code, status FROM cmx_groups WHERE group_id = :group_id LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':group_id', $groupId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }
}


