<?php
/**
 * 后台实时管理选手排名接口
 * 简单通过 admin_key 校验访问权限，正式环境建议改为账号密码 + Session。
 */

require_once __DIR__ . '/../service/RankingService.php';
require_once __DIR__ . '/../model/PlayerModel.php';
require_once __DIR__ . '/../util/Log.php';

header('Content-Type: application/json; charset=utf-8');

$config = require __DIR__ . '/../config/admin.php';
$adminKey = $config['admin_key'] ?? '';

if (empty($adminKey) || ($_GET['key'] ?? $_POST['key'] ?? '') !== $adminKey) {
    echo json_encode(['code' => 401, 'msg' => 'unauthorized'], JSON_UNESCAPED_UNICODE);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    $rankingService = new RankingService();
    $playerModel    = new PlayerModel();

    // 获取指定分组当前排名列表（后台用）
    if ($action === 'list') {
        $groupId = isset($_GET['group_id']) ? (int)$_GET['group_id'] : 0;
        if ($groupId <= 0) {
            echo json_encode(['code' => 400, 'msg' => 'invalid_group_id'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $data = $rankingService->getGroupRankingWithInfo($groupId);
        echo json_encode(['code' => 200, 'msg' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 手动更新单个选手的排名信息
    if ($action === 'update') {
        $input = $_POST;
        $playerId    = isset($input['player_id']) ? (int)$input['player_id'] : 0;
        $groupId     = isset($input['group_id']) ? (int)$input['group_id'] : 0;
        $currentRank = isset($input['current_rank']) ? (int)$input['current_rank'] : 0;
        $lapTime     = $input['lap_time'] !== '' ? (float)$input['lap_time'] : null;
        $gapTime     = $input['gap_time'] !== '' ? (float)$input['gap_time'] : null;

        if ($playerId <= 0 || $groupId <= 0 || $currentRank <= 0) {
            echo json_encode(['code' => 400, 'msg' => 'invalid_params'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $player = $playerModel->getById($playerId);
        if (!$player || (int)$player['group_id'] !== $groupId) {
            echo json_encode(['code' => 404, 'msg' => 'player_not_found'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 复用 RankingService 的 upsert 能力
        $rankingService->syncSingleRanking([
            'car_number'   => $player['car_number'],
            'current_rank' => $currentRank,
            'lap_time'     => $lapTime,
            'gap_time'     => $gapTime,
        ]);

        echo json_encode(['code' => 200, 'msg' => 'success'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode(['code' => 400, 'msg' => 'invalid_action'], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    Log::error('api_admin_ranking_error', ['err' => $e->getMessage()]);
    echo json_encode(['code' => 500, 'msg' => 'server_error'], JSON_UNESCAPED_UNICODE);
}


