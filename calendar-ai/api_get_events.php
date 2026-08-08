<?php

include_once "./db.php";

header('Content-Type: application/json; charset=utf-8');
// 移除不安全的 Access-Control-Allow-Origin: * ，僅允許同源請求

$events = $Events->all("WHERE `deleted_at` IS NULL ORDER BY `event_date` ASC, `start_time` ASC");

echo json_encode($events, JSON_UNESCAPED_UNICODE);

?>