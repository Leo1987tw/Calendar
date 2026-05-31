<?php

date_default_timezone_set("Asia/Taipei");
// echo date("Y/m/d H:i:s l, e");

$today = date("d");
$thisMonth = date("m");
$thisYear = date("Y");
$firstDayOfThisMonth = date("w", strtotime("first day of this month"));
$numberOfDaysThisMonth = date("t");
$numberOfWeeksThisMonth = ceil(($firstDayOfThisMonth + date("t")) / 7);

?>

<div class="calendar">

<?

for($i = 0; $i < $numberOfWeeksThisMonth; $i++){
    for($j = 0; $j < 7; $j++){
        if($i * 7 + $j >= $firstDayOfThisMonth && $i * 7 + $j <= $firstDayOfThisMonth + $numberOfDaysThisMonth - 1){
            echo "<div>";
            echo $i * 7 + $j - $firstDayOfThisMonth + 1;
        }else {
            echo "<div class=\"none\">";
        }
        echo "</div>";
    }
}

?>

</div>