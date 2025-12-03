<?php

require_once __DIR__ . '/../util/Db.php';

class PlayerModel
{
    private $db;

    public function __construct()
    {
        $this->db = Db::getInstance();
    }

    // 新增：按ID获取选手详情（用于展示页）
    public function getPlayerById(int $playerId): ?array
    {
        $sql = "SELECT * FROM cmx_players WHERE player_id = :player_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':player_id' => $playerId]);
        $player = $stmt->fetch(PDO::FETCH_ASSOC);
        return $player ?: null;
    }

    public function getById(int $playerId): ?array
    {
        $sql = 'SELECT * FROM cmx_players WHERE player_id = :player_id LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':player_id', $playerId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getByCarNumber(string $carNumber): ?array
    {
        $sql = 'SELECT * FROM cmx_players WHERE car_number = :car_number LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':car_number', $carNumber, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * 获取指定分组的出场名单（按出场顺序排序）
     */
    public function getEntryListByGroup(int $groupId): array
    {
        $sql = 'SELECT player_id, player_name, car_number, nationality, team_name, entry_order
                FROM cmx_players
                WHERE group_id = :group_id
                ORDER BY 
                    CASE WHEN entry_order IS NULL THEN 1 ELSE 0 END,
                    entry_order ASC,
                    player_id ASC';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':group_id', $groupId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * 更新选手的出场顺序
     */
    public function updateEntryOrder(int $playerId, ?int $entryOrder): bool
    {
        $sql = 'UPDATE cmx_players SET entry_order = :entry_order WHERE player_id = :player_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':player_id', $playerId, PDO::PARAM_INT);
        $stmt->bindValue(':entry_order', $entryOrder, $entryOrder === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        return $stmt->execute();
    }
}


