<div class="input-block left">
    <div>
        <label for="">選擇月份</label>
        <input type="month" id="month" name="month" value="<?= $thisYear . '-' . sprintf('%02d', $thisMonth); ?>" onchange="this.form.submit()">
    </div>
    <div>
        <label for="date">選擇日期</label>
        <input type="date" id="date" name="date" readonly>
    </div>
    <div>
        <label for="">日期名稱</label>
        <input type="text">
    </div>
    <div style="display: flex; justify-content: center; margin: 10px auto;">
        <button>新增</button>
        <button>取消</button>
    </div>
</div>

<div class="main">
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
        <a href="?month=<?= $prevMonth; ?>" class="previous-month">Previous<br>Month</a>
        <div class="this-year"><?= isset($_GET['month']) ? date("Y", strtotime($_GET['month'])) : date("Y") ?></div>
        <div class="this-month"><?= isset($_GET['month']) ? date("M", strtotime($_GET['month'])) : date("M");; ?></div>
        <a href="?month=<?= $nextMonth; ?>" class="next-month">Next<br>Month</a>
    </div>

    <div class="calendar">

        <div class="date weekday column-0">Sunday</div>
        <div class="date weekday column-1">Monday</div>
        <div class="date weekday column-2">Tuesday</div>
        <div class="date weekday column-3">Wednesday</div>
        <div class="date weekday column-4">Thursday</div>
        <div class="date weekday column-5">Friday</div>
        <div class="date weekday column-6">Saturday</div>

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

<div class="input-block right">
    <div>
        <label for="start-time">行程開始</label>
        <input type="time" id="start-time" name="start-time">
    </div>
    <div>
        <label for="end-time">行程結束</label>
        <input type="time" id="end-time" name="end-time">
    </div>
    <div>
        <label for="during-time">行程時長</label>
        <input type="time" id="during-time" name="during-time" readonly>
    </div>
    <div>
        <label for="type">行程類型</label>
        <select name="type" id="type">
            <?php
            $types = $Types->all();
            foreach ($types as $type):
            ?>
                <option id="<?= $type['id']; ?>" value="<?= $type['id']; ?>">
                    <?= $type['name']; ?>
                </option>
            <?php
            endforeach;
            ?>
        </select>
    </div>
    <div>
        <label for="title">行程標題</label>
        <input type="text" id="title" name="title">
    </div>
    <div style="display: flex; align-items: flex-start; margin-bottom: 10px;">
        <label for="description">行程描述</label>
        <textarea name="description" id="description" style="width: 120px; height: 60px; margin-top: 15px; margin-left: 20px; border-radius: 5px;"></textarea>
    </div>
    <div style="margin-top: 10px;">
        <div>
            <label for="color">文字</label>
            <input type="color" name="color" id="color">
        </div>
        <div>
            <label for="background-color">背景</label>
            <input type="color" name="background-color" id="background-color">
        </div>
        <div>
            <label for="border-color">邊線</label>
            <input type="color" name="border-color" id="border-color">
        </div>
    </div>
    <div style="display: flex; justify-content: center; align-items: center; margin: 30px auto;">
        <input type="hidden" id="id" name="id">
        <button type="button" class="event-btn" onclick="addEvent()">新增行程</button>
        <button type="button" class="event-btn" onclick="editEvent()">編輯行程</button>
        <button type="button" class="event-btn" onclick="deleteEvent()">刪除行程</button>
    </div>
</div>

<script>
    var title = document.querySelector('.title');
    var calendar = document.querySelector('.calendar');

    var globalActiveCells = null;

    // 防止連點所設旗標
    let isAnimating = false;

    window.addEventListener('click', function(event) {
        if (isAnimating) return;

        var date = event.target.closest('.date');

        let someCellIsHidden = document.querySelector('.calendar > div.none');

        // 檢查目前月曆是否為展開的狀態
        if (someCellIsHidden) {
            // 點擊表單沒有反應
            if (event.target.closest('.input-block')) return;
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
                    isTimeBlock(event);
                    return;
                } else {
                    isTimeBlock(event);
                    return;
                }
            }

            let activeCell = document.querySelector('.calendar > div.active');
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

            globalActiveCells = null;
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
                        weekdayCell.setAttribute('data-id', cell.dataset.id);
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

            globalActiveCells = thisCells;

            // 呼叫資料庫後進行渲染
            renderEventsToCalendar(thisCells);

        }
    });

    function isTimeBlock(event) {
        if (event.target.closest('.time-block')) {
            const timeBlock = event.target.closest('.time-block');

            document.querySelectorAll('.time-block.checked').forEach(block => block.classList.remove('checked'));

            timeBlock.classList.add('checked');

            const eventId = timeBlock.getAttribute('data-event-id');
            const startTime = timeBlock.getAttribute('data-start');
            const endTime = timeBlock.getAttribute('data-end');

            const type = timeBlock.getAttribute('data-type');
            const title = timeBlock.getAttribute('data-title');
            const description = timeBlock.getAttribute('data-description');

            const color = timeBlock.getAttribute('data-color');
            const backgrounkColor = timeBlock.getAttribute('data-background-color');
            const borderColor = timeBlock.getAttribute('data-border-color');

            $("#id").val(eventId);
            $("#start-time").val(startTime.substring(0, 5));
            $("#end-time").val(endTime.substring(0, 5));

            $("#type").val(type);
            $("#title").val(title);
            $("#description").val(description);

            $("#color").val(color);
            $("#background-color").val(backgrounkColor);
            $("#border-color").val(borderColor);

            if (startTime && endTime) {
                const [startHour, startMinute] = startTime.split(':').map(Number);
                const [endHour, endMinute] = endTime.split(':').map(Number);

                let difference = (endHour * 60 + endMinute) - (startHour * 60 + startMinute);

                const h = String(Math.floor(difference / 60)).padStart(2, '0');
                const m = String(difference % 60).padStart(2, '0');
                $("#during-time").val(`${h}:${m}`);
            }
        }
    }

    function renderEventsToCalendar(cells) {
        cells.forEach(cell => {
            if (!cell.dataset.day) {
                cell.dataset.day = cell.innerHTML;
            }

            let cellId = cell.dataset.id;

            cell.innerText = "";
        });

        fetch('api_get_events.php')
            .then(response => {
                if (!response.ok) throw new Error('network response failed');
                return response.json();
            })
            .then(events => {
                // console.log(events);

                events.forEach(event => {
                    const id = event.id
                    const date = event.event_date;
                    const start = event.start_time.substring(0, 5);
                    const end = event.end_time.substring(0, 5);
                    const type = event.type_id;
                    const title = event.title;
                    const description = event.description;
                    const color = event.color;
                    const backgroundColor = event.background_color;
                    const borderColor = event.border_color;

                    const targetColumn = document.querySelector(`
                        .calendar>.date[data-id="${date}"]:not(.weekday)
                    `);

                    if (targetColumn && targetColumn.classList.contains('active')) {
                        const isAlreadyExist = targetColumn.querySelector(`
                            [data-event-id="${id}"]
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
                        eventElement.setAttribute('data-event-id', id);

                        eventElement.setAttribute('data-start', start);
                        eventElement.setAttribute('data-end', end);

                        eventElement.setAttribute('data-type', type);
                        eventElement.setAttribute('data-title', title);
                        eventElement.setAttribute('data-description', description);

                        eventElement.setAttribute('data-color', color);
                        eventElement.setAttribute('data-background-color', backgroundColor);
                        eventElement.setAttribute('data-border-color', borderColor);

                        eventElement.setAttribute('draggable', 'true');

                        eventElement.addEventListener('dragstart', (event) => {
                            event.dataTransfer.setData('text/plain', id);
                            event.dataTransfer.setData('text/plain', start);
                            event.dataTransfer.setData('text/plain', event.during_time);
                            eventElement.style.opacity = '0.4';
                        });

                        eventElement.addEventListener('dragend', () => {
                            eventElement.style.opacity = '1';
                        })

                        eventElement.style.resize = 'vertical';
                        eventElement.style.overflow = 'hidden';

                        const resizeObserver = new ResizeObserver(entries => {
                            for (let entry of entries) {
                                if (eventElement.dataset.initResize === 'true') {
                                    const currentHeight = entry.contentRect.height;
                                    const pixelsPerMinute = 720 / 1440;
                                    const newDurationMinutes = Math.round(currentHeight / pixelsPerMinute);

                                    const startTimeString = eventElement.getAttribute('data-start');

                                    let [hours, minutes] = startTimeString.split(':').map(Number);
                                    let totalMinutes = hours * 60 + newDurationMinutes;

                                    if (totalMinutes > 1439) totalMinutes = 1439;

                                    const newEndHours = String(Math.floor(totalMinutes / 60)).padStart(2, '0');
                                    const newEndMinutes = String(totalMinutes % 60).padStart(2, '0');
                                    const newEndTimeString = `${newEndHours}:${newEndMinutes}`

                                    $("#end-time").val(newEndTimeString);
                                    $("#event-id").val(id);
                                    $("#start-time").val(startTimeString);

                                    eventElement.setAttribute('data-end', newEndTimeString);

                                    // console.log(`change time:${newEndTimeString}`);
                                }
                            }
                        })

                        eventElement.innerHTML = `
                            <div style="font-size: 12px; opacity: 0.8; margin-top: 2px;">${start}</div>
                            <div style="font-weight: bold; line-height: 1.2;">${type}${title}${description}</div>
                        `;

                        eventElement.style.top = `${topPosition}px`;
                        eventElement.style.height = `${blockHeight}px`;
                        eventElement.style.color = color;
                        eventElement.style.backgroundColor = backgroundColor;
                        eventElement.style.borderColor = borderColor;


                        targetColumn.appendChild(eventElement);

                        resizeObserver.observe(eventElement);

                        eventElement.addEventListener('mousedown', (event) => {
                            if (event.offsetY >= eventElement.clientHeight - 10) {
                                eventElement.dataset.initResize = 'true';
                                event.stopPropagation();
                            }
                        })
                    }
                });
            })
            .catch(error => console.error('fetch failed:', error));
    }

    window.addEventListener('mouseup', () => {
        const resizingElement = document.querySelector('[data-init-resize="true"]');
        if (resizingElement) {
            resizingElement.dataset.initResize = 'false';
        }
    })

    function getDurationInMinutes(duringTime) {
        if (!duringTime) return 0;
        const [hours, minutes, seconds] = duringTime.split(':').map(Number);
        return (hours * 60) + minutes;
    }

    const startTimeInput = document.getElementById('start-time');
    const endTimeInput = document.getElementById('end-time');
    const duringTimeInput = document.getElementById('during-time');

    function calculateDuringTimeInput() {
        var startTimeInputValue = startTimeInput.value;
        var endTimeInputValue = endTimeInput.value;

        if (!startTimeInputValue || !endTimeInputValue) {
            return;
        }

        var [startHour, startMinute] = startTimeInputValue.split(':').map(Number);
        var [endHour, endMinute] = endTimeInputValue.split(':').map(Number);

        var startTotalMinute = startHour * 60 + startMinute;
        var endTotalMinute = endHour * 60 + endMinute;

        if (startTotalMinute > endTotalMinute) {
            endTotalMinute += 1440;
        }

        var differenceTotalMinute = endTotalMinute - startTotalMinute;

        var differenceHour = differenceTotalMinute / 60;
        var differenceMinute = differenceTotalMinute % 60;

        var formattedHour = String(differenceHour).padStart(2, '0');
        var formattedMinute = String(differenceMinute).padStart(2, '0');

        duringTimeInput.value = `${formattedHour}:${formattedMinute}`;
    }

    startTimeInput.addEventListener('input', calculateDuringTimeInput);
    endTimeInput.addEventListener('input', calculateDuringTimeInput);

    function addEvent() {
        // 防止事件冒泡
        event.stopPropagation();
        let date = $("#date").val();
        let startTime = $("#start-time").val();
        let endTime = $("#end-time").val();
        let type = $("#type").val();
        let title = $("#title").val();
        let description = $("#description").val();
        let color = $("#color").val() ?? '#000000';
        let backgroundColor = $("#background-color").val() ?? '#FFFFFF';
        let borderColor = $("#border-color").val() ?? '#000000';

        if (date == "" || startTime == "" || endTime == "") {
            alert("請填入數值");
            return;
        }

        $.post("./api_add_event.php", {
            date,
            startTime,
            endTime,
            type,
            title,
            description,
            color,
            backgroundColor,
            borderColor
        }, () => {
            alert("成功新增一個行程\n您變得更忙了");
            if (globalActiveCells) {
                renderEventsToCalendar(globalActiveCells);
            }
        })
    }

    function editEvent() {
        // 防止事件冒泡
        event.stopPropagation();

        let id = $("#id").val();
        let date = $("#date").val();
        let startTime = $("#start-time").val();
        let endTime = $("#end-time").val();
        let type = $("#type").val();
        let title = $("#title").val();
        let description = $("#description").val();
        let color = $("#color").val() ?? '#000000';
        let backgroundColor = $("#background-color").val() ?? '#FFFFFF';
        let borderColor = $("#border-color").val() ?? '#000000';

        if (date == "" || startTime == "" || endTime == "") {
            alert("請填入數值");
            return;
        }

        $.post("./api_edit_event.php", {
            id,
            date,
            startTime,
            endTime,
            type,
            title,
            description,
            color,
            backgroundColor,
            borderColor
        }, () => {
            alert("成功修改一個行程\n可以這樣改了又改的嗎");
            if (globalActiveCells) {
                renderEventsToCalendar(globalActiveCells);
            }
        })
    }

    function deleteEvent() {
        // 防止事件冒泡
        event.stopPropagation();

        let id = $("#id").val();

        if (id == "" || id == null) {
            alert("需要選擇一個行程");
            return;
        }

        $.post("./api_delete_event.php", {
            id
        }, () => {
            alert("成功刪除一個行程\n您變得更閒了");
            if (globalActiveCells) {
                renderEventsToCalendar(globalActiveCells);
            }
        });
    }
</script>