<?php
include_once "./db.php";
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>互動式萬年曆</title>
    <link rel="stylesheet" href="./style.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="./js/calendar.js" defer></script>
</head>
<body>
    <header class="app-header">
        <div class="header-container">
            <h1 class="app-title">🗓️ 互動式萬年曆</h1>
            <span class="app-subtitle">Interactive Event Management System</span>
        </div>
    </header>
    <main>
        <?php include "./calendar.php"; ?>
    </main>
</body>
</html>