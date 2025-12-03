<?php
require_once __DIR__ . '/../service/RankingService.php';
require_once __DIR__ . '/../util/Log.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$action = $_GET['action'] ?? '';

try {
    if ($action === 'getGroupRanking') {
        $groupId = isset($_GET['group_id']) ? (int)$_GET['group_id'] : 0;
        if ($groupId <= 0) {
            echo json_encode(['code' => 400, 'msg' => 'invalid_group_id'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $service = new RankingService();
        $data = $service->getGroupRankingWithInfo($groupId);

        echo json_encode(['code' => 200, 'msg' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode(['code' => 400, 'msg' => 'invalid_action'], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    Log::error('api_ranking_error', ['err' => $e->getMessage()]);
    echo json_encode(['code' => 500, 'msg' => 'server_error'], JSON_UNESCAPED_UNICODE);
}