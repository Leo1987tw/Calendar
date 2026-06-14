<?php

date_default_timezone_set("Asia/Taipei");
// echo date("Y/m/d H:i:s l, e");

$today = date("d");
$thisMonth = isset($_GET['month']) ? date("m", strtotime($_GET['month'])) : date("m");
$thisYear = isset($_GET['month']) ? date("Y", strtotime($_GET['month'])) : date("Y");
$firstDayOfThisMonth = isset($_GET['month']) ? date("w", strtotime("first day of this month", strtotime($_GET['month']))) : date("w", strtotime("first day of this month"));
$numberOfDaysThisMonth = isset($_GET['month']) ? date("t", strtotime($_GET['month'])) : date("t");
$numberOfWeeksThisMonth = ceil(($firstDayOfThisMonth + $numberOfDaysThisMonth) / 7);

$lastMonth = ($thisMonth == 1) ? $thisYear . "-12" : $thisYear . "-" . ($thisMonth - 1);
$nextMonth = ($thisMonth == 12) ? ($thisYear + 1) . "-1" : $thisYear . "-" . ($thisMonth + 1);

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