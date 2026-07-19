<?php

date_default_timezone_set("Asia/Taipei");
// echo date("Y/m/d H:i:s l, e");

$today = date("d");
$thisMonth = isset($_GET['month']) ? date("m", strtotime($_GET['month'])) : date("m");
$thisYear = isset($_GET['month']) ? date("Y", strtotime($_GET['month'])) : date("Y");
$firstDayOfThisMonth = isset($_GET['month']) ? date("w", strtotime("first day of this month", strtotime($_GET['month']))) : date("w", strtotime("first day of this month"));
$numberOfDaysThisMonth = isset($_GET['month']) ? date("t", strtotime($_GET['month'])) : date("t");
$numberOfWeeksThisMonth = ceil(($firstDayOfThisMonth + $numberOfDaysThisMonth) / 7);

$prevMonth = ($thisMonth == 1) ? $thisYear . "-12" : $thisYear . "-" . ($thisMonth - 1);
$nextMonth = ($thisMonth == 12) ? ($thisYear + 1) . "-1" : $thisYear . "-" . ($thisMonth + 1);

$numberOfDaysPrevMonth = date("t", strtotime($prevMonth));

$prevMonthYear = ($thisMonth == 1) ? $thisYear - 1 : $thisYear;
$nextMonthYear = ($thisMonth == 12) ? $thisYear + 1 : $thisYear;

?>

<div class="subject">
    <div class="month"><?= isset($_GET['month']) ? date("M", strtotime($_GET['month'])) : date("M");; ?></div>
    <div class="year"><?= isset($_GET['month']) ? date("Y", strtotime($_GET['month'])) : date("Y") ?></div>
</div>

<div class="calendar">

    <div class="weekday">Sunday</div>
    <div class="weekday">Monday</div>
    <div class="weekday">Tuesday</div>
    <div class="weekday">Wednesday</div>
    <div class="weekday">Thursday</div>
    <div class="weekday">Friday</div>
    <div class="weekday">Saturday</div>

    <?php

    for ($i = 0; $i < $numberOfWeeksThisMonth; $i++) {
        for ($j = 0; $j < 7; $j++) {
            if ($i * 7 + $j >= $firstDayOfThisMonth && $i * 7 + $j <= $firstDayOfThisMonth + $numberOfDaysThisMonth - 1) {
                $day = $i * 7 + $j - $firstDayOfThisMonth + 1;
                $sday = sprintf("%02d", $day);
                echo "<div class=\"date week-$i\" id=\"$thisYear-$thisMonth-$sday\">";
                echo $day;
            } elseif ($i * 7 + $j < $firstDayOfThisMonth) {
                $day = $numberOfDaysPrevMonth - $firstDayOfThisMonth + 1 + $j;
                $sday = sprintf("%02d", $day);
                echo "<div class=\"date week-$i\" id=\"$prevMonthYear-$prevMonth-$sday\">";
                echo $day;
            } elseif ($i * 7 + $j > $firstDayOfThisMonth + $numberOfDaysThisMonth - 1) {
                $day = $i * 7 + $j - $firstDayOfThisMonth + 1 - $numberOfDaysThisMonth;
                $sday = sprintf("%02d", $day);
                echo "<div class=\"date week-$i\" id=\"$nextMonthYear-$nextMonth-$sday\">";
                echo $day;
            }
            echo "</div>";
        }
    }

    ?>

</div>

<!-- <div class="modal-overlay">
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
</div> -->

<script>
    var calendar = document.querySelector('.calendar');
    var subject = document.querySelector('.subject');
    // var modalOverlay = document.querySelector('.modal-overlay');
    // var modalContent = document.querySelector('.modal-content');
    // var modalText = document.getElementById('modal-text');
    // var modalInput = document.getElementById('modal-input');
    // var closeButton = document.querySelector('.close-btn');

    // var eventForm = modalOverlay.querySelector('form');
    // var eventTitleInput = document.getElementById('event-title');

    // var activeRectangle = null;

    window.addEventListener('click', function(event) {
        var date = event.target.closest('.date');

        // if (!date) {
        //     return;
        // // }

        // var classListArrayOfDate = Array.from(date.classList);
        // var thisWeek = classListArrayOfDate.find(className => className.startsWith('week-'));

        // console.log(thisWeek);

        // if (!date || date.classList.contains('none') || date.classList.contains('weekday')) {
        //     return;
        // }

        // var dateId = date.id;
        // var dateNumber = date.innerText;

        // console.log(dateId);
        // console.log(dateNumber);

        // let thisCells = document.querySelectorAll(`.calendar > .${thisWeek}`);
        // let othersCells = document.querySelectorAll(`.calendar > div:not(.weekday):not(.${thisWeek})`);
        let someCellIsHidden = document.querySelector('.calendar > div.none');

        if (someCellIsHidden) {
            if(event.target.closest('.time-block'))return;

            let activeCell = document.querySelector('.calendar >  div.active');

            if(!activeCell)return;

            var classListArrayOfDate = Array.from(activeCell.classList);
            var thisWeek = classListArrayOfDate.find(className => className.startsWith('week-'));

            let othersCells = document.querySelectorAll(`.calendar > div:not(.weekday):not(.${thisWeek})`);
            let thisCells = document.querySelectorAll(`.calendar > .${thisWeek}`);
            thisCells.forEach(cell => cell.classList.remove('active'));
            othersCells.forEach(cell => {
                cell.classList.add('collapse');
                cell.classList.remove('none');
            });
            subject.classList.add('collapse');
            subject.classList.remove('none');

            setTimeout(() => {
                othersCells.forEach(cell => {
                    cell.classList.remove('collapse');
                });
                subject.classList.remove('collapse');
            }, 10);

            thisCells.forEach(cell => {
                if (cell.dataset.day) {
                    cell.innerHTML = cell.dataset.day;
                }
            });
        } else {
            if (!date) {
                return;
            }

            var classListArrayOfDate = Array.from(date.classList);
            var thisWeek = classListArrayOfDate.find(className => className.startsWith('week-'));

            console.log(thisWeek);

            if (!date || date.classList.contains('none') || date.classList.contains('weekday')) {
                return;
            }

            var dateId = date.id;
            var dateNumber = date.innerText;

            console.log(dateId);
            console.log(dateNumber);

            let thisCells = document.querySelectorAll(`.calendar > .${thisWeek}`);
            let othersCells = document.querySelectorAll(`.calendar > div:not(.weekday):not(.${thisWeek})`);
            thisCells.forEach(cell => cell.classList.add('active'));
            othersCells.forEach(cell => {
                cell.classList.remove('none');
                cell.classList.add('collapse');
            });
            subject.classList.remove('none');
            subject.classList.add('collapse');

            setTimeout(() => {
                othersCells.forEach(cell => {
                    cell.classList.add('none');
                    cell.classList.remove('collapse');
                });
                subject.classList.add('none');
                subject.classList.remove('collapse');
            }, 400);

            thisCells.forEach(cell => {
                if (!cell.dataset.day) {
                    cell.dataset.day = cell.innerHTML;
                }

                let cellId = cell.id;

                cell.innerText = "";

                fetch('api_get_events.php')
                    .then(response => {
                        if (!response.ok) throw new Error('network response failed');
                        return response.json();
                    })
                    .then(events => {
                        console.log("success fetch backend data:", events);
                        renderEventsToCalendar(events);
                    })
                    .catch(error => console.error('fetch failed:', error));
            });
        }

        // modalText.innerText = dateId;
        // modalInput.value = dateId;

        // eventTitleInput.value = '';

        // activeRectangle = date.getBoundingClientRect();
        // modalContent.style.transition = "none";
        // modalContent.style.top = activeRectangle.top + "px";
        // modalContent.style.left = activeRectangle.left + "px";
        // modalContent.style.width = activeRectangle.width + "px";
        // modalContent.style.height = activeRectangle.height + "px";

        // setTimeout(function(){
        //     modalContent.style.transition = "all 0.3s ease";
        //     modalOverlay.classList.add('active');

        //     var targetWidth = 380;
        //     var targetHeight = 320;
        //     var targetTop = (window.innerHeight - targetHeight) / 2;
        //     var targetLeft = (window.innerWidth - targetWidth) / 2;

        //     modalContent.style.top = targetTop + "px";
        //     modalContent.style.left = targetLeft + "px";
        //     modalContent.style.width = targetWidth + "px";
        //     modalContent.style.height = targetHeight + "px";
        //     modalContent.style.opacity = "1";

        //     eventTitleInput.focus();
        // }, 30);
    });

    // eventForm.addEventListener('submit', function(){
    //     event.preventDefault();

    //     var selectedDateId = modalInput.value;
    //     var eventTitle = eventTitleInput.value;

    //     var targetDateBox = document.getElementById(selectedDateId);

    //     if(targetDateBox){
    //         var oldTag = targetDateBox.querySelector('.event-tag');
    //         if(oldTag){
    //             oldTag.remove();
    //         }

    //         var eventTag = document.createElement('div');
    //         eventTag.className = 'event-tag';
    //         eventTag.innerText = eventTitle;

    //         targetDateBox.appendChild(eventTag);
    //     }

    //     closeModal()
    // });

    // function closeModal(){
    //     if(activeRectangle){
    //         modalOverlay.classList.remove('active');
    //         modalContent.style.top = activeRectangle.top + "px";
    //         modalContent.style.left = activeRectangle.left + "px";
    //         modalContent.style.width = activeRectangle.width + "px";
    //         modalContent.style.height = activeRectangle.height + "px";
    //         modalContent.style.opacity = "0";
    //     }
    // }

    // closeButton.addEventListener('click', closeModal);

    // window.addEventListener('click', function(event){
    //     if(event.target == modalOverlay){
    //         closeModal();
    //     }
    // });

    function renderEventsToCalendar(events) {
        events.forEach(event => {
            const date = event.event_date;
            const start = event.start_time.substring(0, 5);
            const end = event.end_time.substring(0, 5);
            const color = event.bg_color;
            const title = event.title;

            const targetColumn = document.getElementById(date);

            if (targetColumn && targetColumn.classList.contains('active')) {
                const isAlreadyExist = targetColumn.querySelector(`[data-event-id="${event.id}"]`);
                if (isAlreadyExist) return;

                const durationMinutes = getDurationInMinutes(event.during_time);
                const startMinutesFromMidnight = getDurationInMinutes(start);
                const pixelsPerMinute = 720 / 1440;

                const topPosition = startMinutesFromMidnight * pixelsPerMinute + 5;
                const blockHeight = durationMinutes * pixelsPerMinute;

                console.log(`event ${title} in ${date} with duration ${durationMinutes}minutes`);

                const eventElement = document.createElement('div');
                eventElement.className = 'date time-block';
                eventElement.setAttribute('data-event-id', event.id);

                eventElement.innerHTML = `
                <div style="font-size: 12px; opacity: 0.8; margin-top: 2px;">${start.substring(0, 5)}</div>
                    <div style="font-weight: bold; line-height: 1.2;">${title}</div>
                `;

                eventElement.style.top = `${topPosition}px`;
                eventElement.style.height = `${blockHeight}px`;
                eventElement.style.backgroundColor = color;


                targetColumn.appendChild(eventElement);
            }
        });
    }

    function getDurationInMinutes(duringTime) {
        if (!duringTime) return 0;
        const [hours, minutes, seconds] = duringTime.split(':').map(Number);
        return (hours * 60) + minutes;
    }
</script>