<?php
require_once __DIR__ . '/../model/PlayerModel.php';
require_once __DIR__ . '/../util/Log.php';
require_once __DIR__ . '/../config/admin.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// 验证管理员密钥
function checkAdminKey(): bool
{
    $config = require __DIR__ . '/../config/admin.php';
    $key = $_GET['key'] ?? $_POST['key'] ?? '';
    return $key === $config['admin_key'];
}

try {
    if (!checkAdminKey()) {
        echo json_encode(['code' => 401, 'msg' => 'invalid_key'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $model = new PlayerModel();

    if ($action === 'list') {
        $groupId = isset($_GET['group_id']) ? (int)$_GET['group_id'] : 0;
        if ($groupId <= 0) {
            echo json_encode(['code' => 400, 'msg' => 'invalid_group_id'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $list = $model->getEntryListByGroup($groupId);
        echo json_encode(['code' => 200, 'msg' => 'success', 'data' => ['entry_list' => $list]], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($action === 'update') {
        $playerId = isset($_POST['player_id']) ? (int)$_POST['player_id'] : 0;
        $entryOrder = isset($_POST['entry_order']) ? (int)$_POST['entry_order'] : null;
        if ($playerId <= 0) {
            echo json_encode(['code' => 400, 'msg' => 'invalid_player_id'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if ($model->updateEntryOrder($playerId, $entryOrder)) {
            echo json_encode(['code' => 200, 'msg' => 'success'], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['code' => 500, 'msg' => 'update_failed'], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    echo json_encode(['code' => 400, 'msg' => 'invalid_action'], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    Log::error('api_admin_entry_order_error', ['err' => $e->getMessage()]);
    echo json_encode(['code' => 500, 'msg' => 'server_error'], JSON_UNESCAPED_UNICODE);
}

