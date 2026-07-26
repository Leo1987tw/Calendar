<div style="margin: 25px;">
    <?php

    date_default_timezone_set("Asia/Taipei");
    echo date("Y/m/d H:i:s l, e");

    $today = date("d");
    $thisMonth = isset($_GET['month']) ? date("m", strtotime($_GET['month'])) : date("m");
    $thisYear = isset($_GET['month']) ? date("Y", strtotime($_GET['month'])) : date("Y");
    $firstDayOfThisMonth = isset($_GET['month']) ? date("w", strtotime("first day of this month", strtotime($_GET['month']))) : date("w", strtotime("first day of this month"));
    $numberOfDaysThisMonth = isset($_GET['month']) ? date("t", strtotime($_GET['month'])) : date("t");
    $numberOfWeeksThisMonth = ceil(($firstDayOfThisMonth + $numberOfDaysThisMonth) / 7);

    $prevMonth = ($thisMonth == 1) ? ($thisYear - 1) . "-12" : $thisYear . "-" . sprintf("%02d", $thisMonth - 1);
    $nextMonth = ($thisMonth == 12) ? ($thisYear + 1) . "-01" : $thisYear . "-" . sprintf("%02d", $thisMonth + 1);

    $numberOfDaysPrevMonth = date("t", strtotime($prevMonth));

    $prevMonthYear = ($thisMonth == 1) ? $thisYear - 1 : $thisYear;
    $nextMonthYear = ($thisMonth == 12) ? $thisYear + 1 : $thisYear;

    ?>
    <div class="title">
        <a href="?month=<?= $prevMonth; ?>" class="previous-month">Previous Month</a>
        <div class="this-year"><?= isset($_GET['month']) ? date("Y", strtotime($_GET['month'])) : date("Y") ?></div>
        <div class="this-month"><?= isset($_GET['month']) ? date("M", strtotime($_GET['month'])) : date("M");; ?></div>
        <a href="?month=<?= $nextMonth; ?>" class="next-month">Next Month</a>
    </div>

    <div class="calendar">

        <div class="weekday date column-0">Sunday</div>
        <div class="weekday date column-1">Monday</div>
        <div class="weekday date column-2">Tuesday</div>
        <div class="weekday date column-3">Wednesday</div>
        <div class="weekday date column-4">Thursday</div>
        <div class="weekday date column-5">Friday</div>
        <div class="weekday date column-6">Saturday</div>

        <?php

        for ($i = 0; $i < $numberOfWeeksThisMonth; $i++) {
            for ($j = 0; $j < 7; $j++) {
                if ($i * 7 + $j >= $firstDayOfThisMonth && $i * 7 + $j <= $firstDayOfThisMonth + $numberOfDaysThisMonth - 1) {
                    $day = $i * 7 + $j - $firstDayOfThisMonth + 1;
                    $sday = sprintf("%02d", $day);
                    echo "<div class=\"date row-$i column-$j\" data-id=\"$thisYear-$thisMonth-$sday\">";
                    echo $day;
                } elseif ($i * 7 + $j < $firstDayOfThisMonth) {
                    $day = $numberOfDaysPrevMonth - $firstDayOfThisMonth + 1 + $j;
                    $sday = sprintf("%02d", $day);
                    echo "<div class=\"date row-$i column-$j\" data-id=\"$prevMonth-$sday\">";
                    echo $day;
                } elseif ($i * 7 + $j > $firstDayOfThisMonth + $numberOfDaysThisMonth - 1) {
                    $day = $i * 7 + $j - $firstDayOfThisMonth + 1 - $numberOfDaysThisMonth;
                    $sday = sprintf("%02d", $day);
                    echo "<div class=\"date row-$i column-$j\" data-id=\"$nextMonth-$sday\">";
                    echo $day;
                }
                echo "</div>";
            }
        }

        ?>

    </div>
</div>

<div class="form-block">
    <form action="" method="get">
        <div>
            <label for="month">選擇月份</label>
            <input type="month" id="month" name="month" value="<?= $thisYear . '-' . sprintf('%02d', $thisMonth); ?>" onchange="this.form.submit()">
        </div>
    </form>
    <form action="./update_event.php" method="post">
        <div>
            <label for="date">選擇日期</label>
            <input type="date" id="date" name="date" readonly>
        </div>
        <div>
            <label for="start-time">開始時間</label>
            <input type="time" id="start-time" name="start-time">
        </div>
        <div>
            <label for="end-time">結束時間</label>
            <input type="time" id="end-time" name="end-time">
        </div>
        <div>
            <label for="during-time">事件期間</label>
            <input type="time" id="during-time" name="during-time" readonly>
        </div>
        <div>
            <select name="events" id="select-event">
                <?php
                $events = $Events->all();
                foreach ($events as $event):
                ?>
                    <option value="<?= $event['id']; ?>">
                        <?= $event['title']; ?>
                    </option>
                <?php
                endforeach;
                ?>
            </select>
        </div>
        <input type="text" id="new-event" placeholder="新事件">
        <button type="button" class="add-event-btn" onclick="addOption()">新增事件</button>
        <button type="button" class="edit-event-btn" onclick="editOption()">編輯事件</button>
        <button type="button" class="delete-event-btn" onclick="deleteOption()">刪除事件</button>
        <input type="reset" value="重置">
        <input type="submit" value="送出">
    </form>
</div>

<script>
    var title = document.querySelector('.title');
    var calendar = document.querySelector('.calendar');

    // 防止連點所設旗標
    let isAnimating = false;

    window.addEventListener('click', function(event) {
        if (isAnimating) return;

        var date = event.target.closest('.date');

        let someCellIsHidden = document.querySelector('.calendar > div.none');

        // 檢查目前月曆是否為展開的狀態
        if (someCellIsHidden) {
            // 點擊表單沒有反應
            if (event.target.closest('.form-block')) return;
            // 點擊日期或星期格進入監聽
            if (date) {
                let dateId = date.dataset.id;

                // console.log(dateId);

                let inputMonth = document.querySelector('input[type="month"]');

                // console.log(inputMonth);

                inputMonth.value = dateId.substring(0, 7);

                let inputDate = document.querySelector('input[type="date"]');

                // console.log(inputDate);

                inputDate.value = dateId;

                if (event.target.closest('.calendar > div:not(.checked)')) {
                    let checkedCell = document.querySelectorAll('.calendar > div.checked');
                    checkedCell.forEach(cell => cell.classList.remove('checked'));
                    let classListArrayOfColumn = Array.from(date.classList);
                    let thisColumn = classListArrayOfColumn.find(className => className.startsWith('column-'));
                    let thisCell = document.querySelectorAll(`.calendar > .${thisColumn}`);
                    thisCell.forEach(cell => cell.classList.add('checked'));
                    isTimeBlock();
                    return
                } else {
                    isTimeBlock();
                    return
                }
            }

            let activeCell = document.querySelector('.calendar >  div.active');
            let checkedCell = document.querySelector('.calendar > div.checked');

            var classListArrayOfRow = Array.from(activeCell.classList);
            var thisRow = classListArrayOfRow.find(className => className.startsWith('row-'));
            var classListArrayOfColumn = Array.from(checkedCell.classList);
            var thisColumn = classListArrayOfColumn.find(className => className.startsWith('column-'));

            let thisCells = document.querySelectorAll(`.calendar > .${thisRow}`);
            let thisCell = document.querySelectorAll(`.calendar > .${thisColumn}`);
            let othersCells = document.querySelectorAll(`.calendar > div:not(.weekday):not(.${thisRow})`);
            thisCells.forEach(cell => cell.classList.remove('active'));
            thisCell.forEach(cell => cell.classList.remove('checked'));
            othersCells.forEach(cell => {
                cell.classList.add('collapse');
                cell.classList.remove('none');
            });
            title.classList.add('collapse');
            title.classList.remove('none');

            // 將星期上面的data-id 拔除
            const weekdayCell = document.querySelectorAll(`
                .calendar>.weekday
            `);
            weekdayCell.forEach(cell => cell.removeAttribute('data-id'));

            isAnimating = true;

            setTimeout(() => {
                othersCells.forEach(cell => {
                    cell.classList.remove('collapse');
                });
                title.classList.remove('collapse');

                isAnimating = false;
            }, 10);

            thisCells.forEach(cell => {
                if (cell.dataset.day) {
                    cell.innerHTML = cell.dataset.day;
                }
            });
        } else {
            // 點擊日期格以外的格子或星期格沒有反應
            if (!date || date.classList.contains('weekday')) {
                return;
            }

            var dateId = date.dataset.id;
            var dateNumber = date.innerText;

            // console.log(dateId);
            // console.log(dateNumber);

            let inputMonth = document.querySelector('input[type="month"]');
            let currentMonth = inputMonth.value;
            let clickedMonth = dateId.substring(0, 7);

            // console.log(inputMonth);
            // console.log(currentMonth);
            // console.log(clickedMonth);

            if (currentMonth && clickedMonth !== currentMonth) {
                inputMonth.value = clickedMonth;
            }

            let inputDate = document.querySelector('input[type="date"]');

            if (inputDate) {
                inputDate.value = dateId;
            }

            console.log(inputDate);

            var classListArrayOfRow = Array.from(date.classList);
            var thisRow = classListArrayOfRow.find(className => className.startsWith('row-'));
            var classListArrayOfColumn = Array.from(date.classList);
            var thisColumn = classListArrayOfColumn.find(className => className.startsWith('column-'));

            // console.log(thisRow);
            // console.log(thisColumn);

            if (!date || date.classList.contains('none') || date.classList.contains('weekday')) {
                return;
            }

            let thisCells = document.querySelectorAll(`.calendar > .${thisRow}`);
            let thisCell = document.querySelectorAll(`.calendar > .${thisColumn}`);
            let othersCells = document.querySelectorAll(`.calendar > div:not(.weekday):not(.${thisRow})`);
            thisCells.forEach(cell => cell.classList.add('active'));
            thisCell.forEach(cell => cell.classList.add('checked'));
            othersCells.forEach(cell => {
                cell.classList.remove('none');
                cell.classList.add('collapse');
            });
            title.classList.remove('none');
            title.classList.add('collapse');

            // 把星期掛上對應日期的data-id
            thisCells.forEach(cell => {
                const classListArray = Array.from(cell.classList);
                const classColumnNumber = classListArray.find(className => className.startsWith('column-'));

                if (classColumnNumber) {
                    const weekdayCell = document.querySelector(`
                        .calendar>.weekday.${classColumnNumber}
                    `);

                    if (weekdayCell) {
                        weekdayCell.setAttribute('data-id', cell.dataset.id)
                    }
                }
            });

            isAnimating = true;

            // 將本週的日期給展開的動畫
            setTimeout(() => {
                othersCells.forEach(cell => {
                    cell.classList.add('none');
                    cell.classList.remove('collapse');
                });
                title.classList.add('none');
                title.classList.remove('collapse');

                isAnimating = false;
            }, 400);

            // 呼叫資料庫後進行渲染
            thisCells.forEach(cell => {
                if (!cell.dataset.day) {
                    cell.dataset.day = cell.innerHTML;
                }

                let cellId = cell.dataset.id;

                cell.innerText = "";

                fetch('api_get_events.php')
                    .then(response => {
                        if (!response.ok) throw new Error('network response failed');
                        return response.json();
                    })
                    .then(events => {
                        // console.log(events);

                        renderEventsToCalendar(events);
                    })
                    .catch(error => console.error('fetch failed:', error));
            });
        }
    });

    function isTimeBlock() {
        if (event.target.closest('.time-block')) {
            const timeBlock = event.target.closest('.time-block');

            document.querySelectorAll('.time-block.checked').forEach(block => block.classList.remove('checked'));

            timeBlock.classList.add('checked');

            const eventId = timeBlock.getAttribute('data-event-id');
            const startTime = timeBlock.getAttribute('data-start');
            const endTime = timeBlock.getAttribute('data-end');

            const selectEvent = document.getElementById('select-event');
            const startInput = document.getElementById('start-time');
            const endInput = document.getElementById('end-time');
            const duringInput = document.getElementById('during-time');

            selectEvent.value = eventId;
            startInput.value = startTime.substring(0, 5);
            endInput.value = endTime.substring(0, 5);

            if (startTime && endTime) {
                const [startHour, startMinute] = startTime.split(':').map(Number);
                const [endHour, endMinute] = endTime.split(':').map(Number);

                let difference = (endHour * 60 + endMinute) - (startHour * 60 + startMinute);

                const h = String(Math.floor(difference / 60)).padStart(2, '0');
                const m = String(difference % 60).padStart(2, '0');
                duringInput.value = `${h}:${m}`;
            }
        }
    }

    function renderEventsToCalendar(events) {
        events.forEach(event => {
            const date = event.event_date;
            const start = event.start_time.substring(0, 5);
            const end = event.end_time.substring(0, 5);
            const color = event.bg_color;
            const title = event.title;

            const targetColumn = document.querySelector(`
                .calendar>.date[data-id="${date}"]:not(.weekday)
            `);

            if (targetColumn && targetColumn.classList.contains('active')) {
                const isAlreadyExist = targetColumn.querySelector(`
                    [data-event-id="${event.id}"]
                `);
                if (isAlreadyExist) return;

                const durationMinutes = getDurationInMinutes(event.during_time);
                const startMinutesFromMidnight = getDurationInMinutes(start);
                const pixelsPerMinute = 720 / 1440;

                const topPosition = startMinutesFromMidnight * pixelsPerMinute + 5;
                const blockHeight = durationMinutes * pixelsPerMinute;

                // console.log(`event ${title} in ${date} with duration ${durationMinutes}minutes`);

                const eventElement = document.createElement('div');
                eventElement.className = 'time-block';
                eventElement.setAttribute('data-event-id', event.id);

                eventElement.setAttribute('data-start', start);
                eventElement.setAttribute('data-end', end);

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