<?php
require_once __DIR__ . '/../model/DisplayModel.php';
require_once __DIR__ . '/../model/PlayerModel.php';
$adminConfig = require __DIR__ . '/../config/admin.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$action = $_GET['action'] ?? '';
$key = $_POST['key'] ?? $_GET['key'] ?? '';
$validKey = $adminConfig['admin_key'] ?? 'default_admin_key';

// 如果是设置展示选手的操作，必须校验密钥
if ($action === 'setDisplayPlayer') {
    if (empty($key) || $key !== $validKey) {
        echo json_encode(['code' => 403, 'msg' => '密钥错误或缺失']);
        exit;
    }
}

$displayModel = new DisplayModel();
$playerModel = new PlayerModel();

switch ($action) {
    case 'setDisplayPlayer':
        if (empty($key)) {
            echo json_encode(['code' => 403, 'msg' => '需要密钥']);
            exit;
        }
        $playerId = (int)($_POST['player_id'] ?? $_GET['player_id'] ?? 0);
        if ($playerId > 0) {
            $result = $displayModel->setDisplayPlayer($playerId);
            if ($result) {
                echo json_encode(['code' => 200, 'msg' => '设置成功']);
            } else {
                echo json_encode(['code' => 500, 'msg' => '设置失败']);
            }
        } else {
            echo json_encode(['code' => 400, 'msg' => '无效的选手ID']);
        }
        break;

    case 'getDisplayConfig':
        $config = $displayModel->getDisplayConfig();
        if ($config && $config['display_type'] === 'player' && $config['target_id'] > 0) {
            $player = $playerModel->getPlayerById($config['target_id']);
            if ($player) {
                $config['player_data'] = $player;
            }
        }
        echo json_encode(['code' => 200, 'data' => $config]);
        break;

    default:
        echo json_encode(['code' => 404, 'msg' => '无效的操作']);
        break;
}