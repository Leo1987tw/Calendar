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

<div class="weekday">Sunday</div>
<div class="weekday">Monday</div>
<div class="weekday">Tuesday</div>
<div class="weekday">Wednesday</div>
<div class="weekday">Thursday</div>
<div class="weekday">Friday</div>
<div class="weekday">Saturday</div>

<?php

for($i = 0; $i < $numberOfWeeksThisMonth; $i++){
    for($j = 0; $j < 7; $j++){
        if($i * 7 + $j >= $firstDayOfThisMonth && $i * 7 + $j <= $firstDayOfThisMonth + $numberOfDaysThisMonth - 1){
            $day = $i * 7 + $j - $firstDayOfThisMonth + 1;
            echo "<div class=\"date\" id=\"$thisYear-$thisMonth-$day\">";
            echo $i * 7 + $j - $firstDayOfThisMonth + 1;
        }else {
            echo "<div class=\"none\">";
        }
        echo "</div>";
    }
}

?>

</div>

<div class="modal-overlay">
    <div class="modal-content">
        <span class="close-btn" style="float: right;">&times;</span>
        <h3>標題</h3>
        <p id="modal-text"></p>

        <form>
            <input type="hidden" id="modal-input" name="event_date">
            <div class="form-group">
                <label for="event-title">內容</label>
                <input type="textarea" id="event-title" name="event_title" placeholder="">
            </div>
            <button type="submit" class="submit-btn">按鈕</button>
        </form>
    </div>
</div>

<script>
    var calendar = document.querySelector('.calendar');
    var modalOverlay = document.querySelector('.modal-overlay');
    var modalContent = document.querySelector('.modal-content');
    var modalText = document.getElementById('modal-text');
    var modalInput = document.getElementById('modal-input');
    var closeButton = document.querySelector('.close-btn');

    var eventForm = modalOverlay.querySelector('form');
    var eventTitleInput = document.getElementById('event-title');

    var activeRectangle = null;

    calendar.addEventListener('click', function(event){
        var date = event.target.closest('.date');

        if(!date || date.classList.contains('none') || date.classList.contains('weekday')){
            return;
        }

        var dateId = date.id;
        var dateNumber = date.innerText;

        modalText.innerText = dateId;
        modalInput.value = dateId;

        eventTitleInput.value = '';

        activeRectangle = date.getBoundingClientRect();
        modalContent.style.transition = "none";
        modalContent.style.top = activeRectangle.top + "px";
        modalContent.style.left = activeRectangle.left + "px";
        modalContent.style.width = activeRectangle.width + "px";
        modalContent.style.height = activeRectangle.height + "px";

        setTimeout(function(){
            modalContent.style.transition = "all 0.3s ease";
            modalOverlay.classList.add('active');

            var targetWidth = 380;
            var targetHeight = 320;
            var targetTop = (window.innerHeight - targetHeight) / 2;
            var targetLeft = (window.innerWidth - targetWidth) / 2;

            modalContent.style.top = targetTop + "px";
            modalContent.style.left = targetLeft + "px";
            modalContent.style.width = targetWidth + "px";
            modalContent.style.height = targetHeight + "px";
            modalContent.style.opacity = "1";

            eventTitleInput.focus();
        }, 30);
    });

    eventForm.addEventListener('submit', function(){
        event.preventDefault();

        var selectedDateId = modalInput.value;
        var eventTitle = eventTitleInput.value;

        var targetDateBox = document.getElementById(selectedDateId);

        if(targetDateBox){
            var oldTag = targetDateBox.querySelector('.event-tag');
            if(oldTag){
                oldTag.remove();
            }

            var eventTag = document.createElement('div');
            eventTag.className = 'event-tag';
            eventTag.innerText = eventTitle;

            targetDateBox.appendChild(eventTag);
        }

        closeModal()
    });

    function closeModal(){
        if(activeRectangle){
            modalOverlay.classList.remove('active');
            modalContent.style.top = activeRectangle.top + "px";
            modalContent.style.left = activeRectangle.left + "px";
            modalContent.style.width = activeRectangle.width + "px";
            modalContent.style.height = activeRectangle.height + "px";
            modalContent.style.opacity = "0";
        }
    }

    closeButton.addEventListener('click', closeModal);

    window.addEventListener('click', function(event){
        if(event.target == modalOverlay){
            closeModal();
        }
    });
</script>