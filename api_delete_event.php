<?php

include_once "./db.php";

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = trim($_POST['id'] ?? '');

    if (empty($id)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => '缺少行程 ID'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $result = $Events->softDel($id);

    if ($result) {
        echo json_encode(['status' => 'success', 'message' => '成功刪除行程'], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => '刪除失敗'], JSON_UNESCAPED_UNICODE);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed'], JSON_UNESCAPED_UNICODE);
}

?>