<?php

include "./db.php";

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $result = $Events->del($_POST['id']);

    if($result){
        echo json_encode(['status' => 'success', 'message' => '刪除成功']);
    }else {
        echo json_encode(['status' => 'error', 'message' => '刪除失敗']);
    }
}

?>