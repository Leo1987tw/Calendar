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
        <div>
            <form action="./index.php" method="GET">
                <input type="month" name="month">
                <input type="submit">
            </form>
        </div>
        <div class="subject">
            <div class="month"><?= isset($_GET['month']) ? date("M", strtotime($_GET['month'])) : date("M");;?></div>
            <div class="year"><?= isset($_GET['month']) ? date("Y", strtotime($_GET['month'])) : date("Y")?></div>
        </div>
        <?php

        include "./calendar.php";

        ?>
    </main>
    <div class="left"><a href="?month=<?= $lastMonth;?>">Last Month</a></div>
    <div class="right"><a href="?month=<?= $nextMonth;?>">Next Month</a></div>
</body>
</html>