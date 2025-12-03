<?php
require_once __DIR__ . '/../model/SponsorModel.php';
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

    $model = new SponsorModel();

    if ($action === 'list') {
        $list = $model->getAll();
        echo json_encode(['code' => 200, 'msg' => 'success', 'data' => ['sponsor_list' => $list]], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($action === 'add') {
        $sponsorName = $_POST['sponsor_name'] ?? '';
        $logoUrl = $_POST['logo_url'] ?? '';
        $displayOrder = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
        $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;

        if (empty($sponsorName) || empty($logoUrl)) {
            echo json_encode(['code' => 400, 'msg' => 'missing_fields'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $id = $model->add($sponsorName, $logoUrl, $displayOrder, $status);
        echo json_encode(['code' => 200, 'msg' => 'success', 'data' => ['sponsor_id' => $id]], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($action === 'update') {
        $sponsorId = isset($_POST['sponsor_id']) ? (int)$_POST['sponsor_id'] : 0;
        $sponsorName = $_POST['sponsor_name'] ?? '';
        $logoUrl = $_POST['logo_url'] ?? '';
        $displayOrder = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
        $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;

        if ($sponsorId <= 0 || empty($sponsorName) || empty($logoUrl)) {
            echo json_encode(['code' => 400, 'msg' => 'invalid_params'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if ($model->update($sponsorId, $sponsorName, $logoUrl, $displayOrder, $status)) {
            echo json_encode(['code' => 200, 'msg' => 'success'], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['code' => 500, 'msg' => 'update_failed'], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    if ($action === 'delete') {
        $sponsorId = isset($_POST['sponsor_id']) ? (int)$_POST['sponsor_id'] : 0;
        if ($sponsorId <= 0) {
            echo json_encode(['code' => 400, 'msg' => 'invalid_sponsor_id'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if ($model->delete($sponsorId)) {
            echo json_encode(['code' => 200, 'msg' => 'success'], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['code' => 500, 'msg' => 'delete_failed'], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    echo json_encode(['code' => 400, 'msg' => 'invalid_action'], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    Log::error('api_admin_sponsor_error', ['err' => $e->getMessage()]);
    echo json_encode(['code' => 500, 'msg' => 'server_error'], JSON_UNESCAPED_UNICODE);
}

