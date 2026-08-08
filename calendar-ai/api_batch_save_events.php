<?php

include_once "./db.php";

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 讀取 POST 的 events 資料 (可為 JSON 字串或 POST 陣列)
    $rawEvents = $_POST['events'] ?? null;

    if (is_string($rawEvents)) {
        $events = json_decode($rawEvents, true);
    } else {
        $events = $rawEvents;
    }

    if (empty($events) || !is_array($events)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => '缺少欲儲存的行程資料'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    global $pdo;
    $successCount = 0;

    try {
        $pdo->beginTransaction();

        $sql = "UPDATE `events` SET 
                    `event_date` = :event_date,
                    `start_time` = :start_time,
                    `end_time` = :end_time,
                    `type_id` = :type,
                    `title` = :title,
                    `description` = :description,
                    `color` = :color,
                    `background_color` = :background_color,
                    `border_color` = :border_color,
                    `updated_at` = :updated_at
                WHERE `id` = :id AND `deleted_at` IS NULL";

        $stmt = $pdo->prepare($sql);

        foreach ($events as $item) {
            if (empty($item['id'])) continue;

            $stmt->execute([
                ':id'               => $item['id'],
                ':event_date'       => $item['date'] ?? $item['event_date'] ?? date('Y-m-d'),
                ':start_time'       => $item['startTime'] ?? $item['start_time'] ?? '00:00',
                ':end_time'         => $item['endTime'] ?? $item['end_time'] ?? '00:00',
                ':type_id'             => $item['type_id'] ?? '',
                ':title'            => $item['title'] ?? '',
                ':description'      => $item['description'] ?? '',
                ':color'            => $item['color'] ?? '#000000',
                ':background_color' => $item['backgroundColor'] ?? $item['background_color'] ?? '#ffffff',
                ':border_color'     => $item['borderColor'] ?? $item['border_color'] ?? '#3b82f6',
                ':updated_at'       => date('Y-m-d H:i:s')
            ]);

            $successCount++;
        }

        $pdo->commit();

        echo json_encode([
            'status' => 'success',
            'message' => "成功批次儲存 {$successCount} 個已修改行程",
            'count' => $successCount
        ], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => '批次儲存失敗：' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed'], JSON_UNESCAPED_UNICODE);
}

?>
