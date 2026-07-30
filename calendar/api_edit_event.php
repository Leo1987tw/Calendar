<?php

include "./db.php";

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $data = [
        'id' => $_POST['id'], 
        'event_date' => $_POST['date'], 
        'start_time' => $_POST['startTime'], 
        'end_time' => $_POST['endTime'], 
        'type_id' => $_POST['type'], 
        'title' => $_POST['title'], 
        'description' => $_POST['description'], 
        'color' => $_POST['color'], 
        'background_color' => $_POST['backgroundColor'], 
        'border_color' => $_POST['borderColor'], 
        'created_at' => date("Y-m-d H:i:s")
    ];

    $result = $Events->save($data);

    if($result){
        echo json_encode(['status' => 'success', 'message' => '新增成功']);
    }else {
        echo json_encode(['status' => 'error', 'message' => '新增失敗']);
    }
}

?>