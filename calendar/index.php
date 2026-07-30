<?php

include_once "./db.php";

?>

<!DOCTYPE html>
<html lang="zh_TW">

<head>
    <meta charset="zh-TW">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>萬年曆</title>
    <link rel="stylesheet" href="./style.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
</head>

<body>
    <main>
        <?php
        
        include "./calendar.php";

        ?>
    </main>
</body>

</html>