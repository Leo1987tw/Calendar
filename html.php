<!DOCTYPE html>
<html lang="zh_TW">
<head>
    <meta charset="zh-TW">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>萬年曆</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>
    <main>
        <div class="subject">
            <div class="month"><?= date("M");?></div>
            <div class="year"><?= date("Y")?></div>
        </div>
        <?php

        include "./calendar.php";

        ?>
    </main>
    <div class="left"></div>
    <div class="right"></div>
</body>
</html>