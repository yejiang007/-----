<?php

require_once __DIR__ . '/../model/PlayerModel.php';
require_once __DIR__ . '/../model/GroupModel.php';
require_once __DIR__ . '/../model/RankingModel.php';
require_once __DIR__ . '/../util/Db.php';

class RankingService
{
    private PlayerModel $playerModel;
    private GroupModel $groupModel;
    private RankingModel $rankingModel;
    private PDO $db;

    public function __construct()
    {
        $this->playerModel  = new PlayerModel();
        $this->groupModel   = new GroupModel();
        $this->rankingModel = new RankingModel();
        $this->db           = Db::getInstance();
    }

    /**
     * 返回指定分组的分组信息 + 当前排名列表
     */
    public function getGroupRankingWithInfo(int $groupId): array
    {
        $groupInfo = $this->groupModel->getById($groupId);
        if (!$groupInfo) {
            throw new RuntimeException('group_not_found');
        }
        $rankingList = $this->rankingModel->getGroupRanking($groupId);
        return [
            'group_info'   => $groupInfo,
            'ranking_list' => $rankingList,
        ];
    }

    /**
     * 处理赛事计时系统推送过来的单条数据
     */
    public function syncSingleRanking(array $payload): void
    {
        if (empty($payload['car_number']) || !isset($payload['current_rank'])) {
            throw new InvalidArgumentException('missing car_number or current_rank');
        }

        $player = $this->playerModel->getByCarNumber($payload['car_number']);
        if (!$player) {
            throw new RuntimeException('player_not_found: ' . $payload['car_number']);
        }

        $playerId = (int)$player['player_id'];
        $groupId  = (int)$player['group_id'];

        $currentRank = (int)$payload['current_rank'];
        $lapTime     = isset($payload['lap_time']) ? (float)$payload['lap_time'] : null;
        $gapTime     = isset($payload['gap_time']) ? (float)$payload['gap_time'] : null;

        $this->db->beginTransaction();
        try {
            $this->rankingModel->upsertRanking($playerId, $groupId, $currentRank, $lapTime, $gapTime);
            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}


