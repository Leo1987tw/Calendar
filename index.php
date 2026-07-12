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
</head>

<body>
    <main>
        <div style="position: fixed; left: -200px;">
            <form action="./index.php" method="GET">
                <input type="month" name="month">
                <input type="submit">
            </form>
        </div>
        <?php

        include "./calendar.php";

        ?>
    </main>
    <div class="left">
        <a href="?month=<?= $prevMonth; ?>">Last Month</a>
        <a href="?month=<?= $nextMonth; ?>">Next Month</a>
    </div>
    <div class="right">
        <form action="./update_event.php" method="post">
            <label for="event-date">事件日期</label>
            <input type="date" id="event-date" name="event-date"><br>
            <label for="start-time">開始時間</label>
            <input type="time" id="start-time" name="start-time"><br>
            <label for="end-time">結束時間</label>
            <input type="time" id="end-time" name="end-time">
            <label for="during-time">事件期間</label>
            <input type="text" id="during-time" name="during-time" readonly>
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
            <input type="text" id="new-event" placeholder="新事件">
            <button type="button" class="add-event-btn" onclick="addOption()">新增事件</button>
            <button type="button" class="edit-event-btn" onclick="editOption()">編輯事件</button>
            <button type="button" class="delete-event-btn" onclick="deleteOption()">刪除事件</button>
            <input type="reset" value="重置">
            <input type="submit" value="送出">
        </form>
    </div>
    <div id="toast-modal" style="display: none; position: fixed; top: 0px; left: 0px; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 9999;justify-content: center; align-items: center;">
        <div style="background: white; padding: 25px; border-radius: 12px; width: 320px; box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.3); border: 2px solid black;">
            <h3 id="toast-title" style="margin-top: 0px; color: black;">事件視窗</h3>
            <hr>
            <form action="" id="toast-form" method="post">
                <input type="hidden" id="toast-event-id" name="id" value="">
                <label for="" style="display: block; margin-bottom: 8px; font-weight: bold;">事件名稱</label>
                <input type="text" id="toast-event-title" name="title" placeholder="請輸入名稱" style="box-sizing: border-box; width: 100%; padding: 8px; margin-bottom: 15px; border: 1px solid white; border-radius: 4px" required>
                <textarea name="description" id="toast-event-description"></textarea>
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" onclick="closeToast()" style="padding: 6px 12px; background: white; border: none; border-radius: 4px; cursor: pointer;">取消</button>
                    <button type="submit" style="padding: 6px 12px; background: green; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">確定</button>
                </div>
            </form>
        </div>
    </div>
    </div>
    <script>
        const startTimeInput = document.getElementById("start-time");
        const endTimeInput = document.getElementById("end-time");
        const duringTimeInput = document.getElementById("during-time");

        const newEvent = document.getElementById("new-event");
        const selectEvent = document.getElementById("select-event");

        const toastModal = document.getElementById("toast-modal");
        const toastForm = document.getElementById("toast-form");
        const toastTitle = document.getElementById("toast-title");
        const toastEventId = document.getElementById("toast-event-id");
        const toastEventTitle = document.getElementById("toast-event-title");

        function calculateDuration() {
            const startTimeValue = startTimeInput.value;
            const endTimeValue = endTimeInput.value;

            if (startTimeValue && endTimeValue) {
                const [startHours, startMinutes] = startTimeValue.split(":").map(Number);
                const [endHours, endMinutes] = endTimeValue.split(":").map(Number);
                const startMinutesNumber = (startHours * 60) + startMinutes;
                const endMinutesNumber = (endHours * 60) + endMinutes;

                const diffMinutesNumber = endMinutesNumber - startMinutesNumber;

                if (diffMinutesNumber < 0) {
                    duringTimeInput.value = "結束時間不能早於開始時間";
                } else {
                    const hours = Math.floor(diffMinutesNumber / 60);
                    const minutes = diffMinutesNumber % 60;

                    const formatHours = String(hours).padStart(2, "0");
                    const formatMinutes = String(minutes).padStart(2, "0");

                    duringTimeInput.value = `${formatHours}:${formatMinutes}:00`;
                }
            } else {
                duringTimeInput.value = "";
            }
        }

        startTimeInput.addEventListener("change", calculateDuration);
        endTimeInput.addEventListener("change", calculateDuration);

        function addOption() {
            toastTitle.innerText = "新增事件";
            toastForm.action = "./add_event.php";
            toastEventId.value = "";
            toastEventTitle.value = "";
            toastEventTitle.placeholder = "請輸入事件名稱";

            toastModal.style.display = "flex";
        }

        function editOption() {
            if (selectEvent.selectedIndex === -1) {
                alert("請選擇要編輯的事件");
                return;
            }

            const selectedOption = selectEvent.options[selectEvent.selectedIndex];

            toastTitle.innerText = "編輯事件";
            toastForm.action = "./edit_event.php";
            toastEventId.value = selectedOption.value;
            toastEventTitle.value = selectedOption.text;

            toastModal.style.display = "flex";
        }

        function closeToast() {
            toastModal.style.display = "none";
        }

        function deleteOption() {
            if (selectEvent.selectedIndex === -1) {
                alert("請選擇要刪除的事件");
                return;
            }

            const selectedIndex = selectEvent.selectedIndex;
            const confirmDelete = confirm(`確定要刪除${selectEvent.options[selectedIndex].text}嗎？`);

            if (confirmDelete) {
                selectEvent.remove(selectedIndex);
            }
        }
    </script>
</body>

</html>