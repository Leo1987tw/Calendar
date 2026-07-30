<?php

include_once "./db.php";

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$events = $Events->all(" WHERE `deleted_at` IS NULL");

echo json_encode($events, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

?>