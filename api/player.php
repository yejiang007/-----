<?php
require_once __DIR__ . '/../model/PlayerModel.php';
require_once __DIR__ . '/../util/Log.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? '';

try {
    $model = new PlayerModel();

    if ($action === 'getDetail') {
        $playerId = isset($_GET['player_id']) ? (int)$_GET['player_id'] : 0;
        if ($playerId <= 0) {
            echo json_encode(['code' => 400, 'msg' => 'invalid_player_id'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $info = $model->getById($playerId);
        if (!$info) {
            echo json_encode(['code' => 404, 'msg' => 'player_not_found'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        echo json_encode(['code' => 200, 'msg' => 'success', 'data' => $info], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode(['code' => 400, 'msg' => 'invalid_action'], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    Log::error('api_player_error', ['err' => $e->getMessage()]);
    echo json_encode(['code' => 500, 'msg' => 'server_error'], JSON_UNESCAPED_UNICODE);
}