<?php

require_once __DIR__ . '/../util/Db.php';

class RankingModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Db::getInstance();
    }

    /**
     * 获取指定分组的当前排名列表
     */
    public function getGroupRanking(int $groupId): array
    {
        $sql = 'SELECT 
                    r.player_id,
                    p.player_name,
                    p.avatar_url,
                    p.car_number,
                    p.nationality,
                    p.team_name,
                    p.entry_number,
                    r.current_rank,
                    r.lap_time,
                    r.gap_time,
                    r.update_time
                FROM cmx_realtime_ranking r
                INNER JOIN cmx_players p ON r.player_id = p.player_id
                WHERE r.group_id = :group_id
                ORDER BY r.current_rank ASC';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':group_id', $groupId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * 插入或更新单个选手的实时排名
     */
    public function upsertRanking(
        int $playerId,
        int $groupId,
        int $currentRank,
        ?float $lapTime,
        ?float $gapTime
    ): void {
        $sql = 'INSERT INTO cmx_realtime_ranking
                    (player_id, group_id, current_rank, lap_time, gap_time, update_time)
                VALUES
                    (:player_id, :group_id, :current_rank, :lap_time, :gap_time, NOW())
                ON DUPLICATE KEY UPDATE
                    current_rank = VALUES(current_rank),
                    lap_time     = VALUES(lap_time),
                    gap_time     = VALUES(gap_time),
                    update_time  = NOW()';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':player_id', $playerId, PDO::PARAM_INT);
        $stmt->bindValue(':group_id', $groupId, PDO::PARAM_INT);
        $stmt->bindValue(':current_rank', $currentRank, PDO::PARAM_INT);
        $stmt->bindValue(':lap_time', $lapTime, $lapTime === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':gap_time', $gapTime, $gapTime === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->execute();
    }
}


