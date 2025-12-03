<?php
require_once __DIR__ . '/../model/GroupModel.php';
require_once __DIR__ . '/../util/Log.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? '';

try {
    $model = new GroupModel();

    if ($action === 'getList') {
        $data = $model->getList();
        echo json_encode(['code' => 200, 'msg' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode(['code' => 400, 'msg' => 'invalid_action'], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    Log::error('api_group_error', ['err' => $e->getMessage()]);
    echo json_encode(['code' => 500, 'msg' => 'server_error'], JSON_UNESCAPED_UNICODE);
}