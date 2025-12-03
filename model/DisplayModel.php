<?php
require_once __DIR__ . '/../util/Db.php';

class DisplayModel {
    private $db;

    public function __construct() {
        $this->db = Db::getInstance();
    }

    /**
     * 设置当前要显示的选手
     * @param int $playerId
     * @return bool
     */
    public function setDisplayPlayer(int $playerId): bool {
        // 先检查是否存在记录
        $checkSql = "SELECT COUNT(*) FROM cmx_display_control WHERE id = 1";
        $count = $this->db->query($checkSql)->fetchColumn();
        
        if ($count > 0) {
            // 更新现有记录
            $sql = "UPDATE cmx_display_control SET target_id = :player_id, display_type = 'player', update_time = NOW() WHERE id = 1";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':player_id' => $playerId]);
        } else {
            // 插入新记录
            $sql = "INSERT INTO cmx_display_control (id, display_type, target_id, group_id, update_time) VALUES (1, 'player', :player_id, 0, NOW())";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':player_id' => $playerId]);
        }
    }

    /**
     * 获取当前显示配置
     * @return array|null
     */
    public function getDisplayConfig(): ?array {
        $sql = "SELECT display_type, target_id, group_id, update_time FROM cmx_display_control WHERE id = 1";
        $stmt = $this->db->query($sql);
        $config = $stmt->fetch(PDO::FETCH_ASSOC);
        return $config ?: null;
    }
}