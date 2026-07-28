# 行程 UI/UX 拖曳互動、表單連動與 Ctrl+Z 復原功能設計方案

本文件針對「行程拖曳移動」、「雙向長度拉伸」、「即時表單連動」、「已修改標籤」以及「Ctrl + Z 復原功能」提供完整的技術做法與架構規劃。

---

## 🎯 需求目標

1. **行程拖曳移動 (Drag-to-Move)**：
   - 滑鼠按住行程區塊可在當天時間軸內**上下拖曳**以改變「開始/結束時間」。
   - 可將行程區塊**跨天拖曳**至其他日期欄位（如從週一拖至週三）。
2. **行程長度雙向拉伸 (Drag-to-Resize)**：
   - 行程區塊頂部新增 Resize Handle：拖曳調整「開始時間」（結束時間不變）。
   - 行程區塊底部新增 Resize Handle：拖曳調整「結束時間」（開始時間不變）。
3. **即時表單連動 (Real-time Form Sync)**：
   - 拖曳過程中與拖曳結束時，頁面表單（ID、日期、開始時間、結束時間、時長）自動即時更新對應數值。
   - 此操作完全在純前端運作，暫不自動寫入後端。
4. **已拖曳修改標籤 (Modified Badge)**：
   - 只要行程被移動或調整過長度，卡片右上角自動顯示 `[已修改]` 標籤與提示樣式。
5. **Ctrl + Z 復原機制 (Undo Action History)**：
   - 紀錄使用者的拖曳與拉伸軌跡。
   - 按下 `Ctrl + Z` (Cmd + Z) 時，可還原上一次的移動/拉伸位置，並同步還原表單內容與標籤狀態。

---

## 🛠️ 技術做法與關鍵模組設計

### 一、 拖曳移動與雙向拉伸 (Pointer Events & Grid Snap)

建議採用 **HTML5 Pointer Events (`pointerdown`, `pointermove`, `pointerup`)** 進行實作，相較於傳統 HTML5 Drag-and-Drop，Pointer Events 能提供更高精確度的位置算術與無縫的滑鼠吸附體驗。

#### 1. 時間與像素換算 (Scale & Snap Grid)
- 日曆時間軸高度為 `720px`，代表 24 小時（`1440 分鐘`）。
- **比例尺**：`pixelsPerMinute = 720 / 1440 = 0.5 px/min`（`1 分鐘 = 0.5 像素`，`1 小時 = 30 像素`）。
- **吸附對齊 (Snap Grid)**：可設定以 `15 分鐘`（`7.5px`）或 `5 分鐘`（`2.5px`）為最小吸附單位，讓拖曳移動更平滑且對齊整齊時間。

```js
// 像素換算時間範例
function pixelsToMinutes(pixels) {
    const rawMinutes = Math.round(pixels / 0.5);
    const snapMinutes = Math.round(rawMinutes / 15) * 15; // 15 分鐘吸附
    return Math.min(1439, Math.max(0, snapMinutes));
}
```

#### 2. 拖曳移動 (Move Across Time & Days)
- **當天移動**：監聽 `pointermove` 的 `deltaY`，即時更新 `eventElement.style.top`。
- **跨天移動**：在 `pointermove` 時使用 `document.elementFromPoint(x, y)` 偵測滑鼠當前懸停的日期欄位 (`.date[data-id]`)；若移至新日期，將 `eventElement` 動態移入新的 `.date` 容器中。

#### 3. 雙向長度拉伸 (Top & Bottom Resize Handles)
- 卡片內放置兩個控制點：
  - `.resize-handle-top` (頂部控制條)
  - `.resize-handle-bottom` (底部控制條)
- **頂部拖曳**：固定原本的 `end_time`，向上/下拖曳時改變 `topPosition` 與 `height`，並重算 `start_time`。
- **底部拖曳**：固定原本的 `start_time`，向上/下拖曳時改變 `height`，並重算 `end_time`。

---

### 二、 即時表單連動 (Real-time Form Sync)

在 `pointermove` (拖曳中) 與 `pointerup` (放開時) 事件觸發時，呼叫全域更新函數：

```js
function syncFormWithEvent(eventId, dateStr, startTimeStr, endTimeStr) {
    $("#id").val(eventId);
    $("#date").val(dateStr);
    $("#start-time").val(startTimeStr);
    $("#end-time").val(endTimeStr);
    
    // 自動計算時長 (HH:MM)
    const [sH, sM] = startTimeStr.split(':').map(Number);
    const [eH, eM] = endTimeStr.split(':').map(Number);
    let diff = (eH * 60 + eM) - (sH * 60 + sM);
    if (diff < 0) diff = 0;
    const h = String(Math.floor(diff / 60)).padStart(2, '0');
    const m = String(diff % 60).padStart(2, '0');
    $("#during-time").val(`${h}:${m}`);
}
```

---

### 三、 已拖曳/修改標籤 (Modified Badge)

#### 1. 標籤狀態管理
- 每當行程位置或長度發生變更且與初始載入值不同時：
  - 設定 HTML 屬性：`eventElement.setAttribute('data-modified', 'true')`
- CSS 樣式：
```css
.time-block[data-modified="true"]::after {
    content: "已修改";
    position: absolute;
    top: 2px;
    right: 4px;
    font-size: 10px;
    background: #f59e0b;
    color: white;
    padding: 1px 4px;
    border-radius: 4px;
    font-weight: bold;
}
```

---

### 四、 Ctrl + Z 復原機制 (Undo Action Stack)

#### 1. 歷史紀錄結構
建立全域歷史堆疊陣列 `undoStack = []`（建議限制最多 30 步）：

```javascript
// 歷史紀錄物件範例
const actionRecord = {
    type: 'MOVE_OR_RESIZE',
    eventId: '15',
    element: eventElement,
    
    // 變更前狀態
    prev: {
        date: '2026-07-28',
        startTime: '09:00',
        endTime: '11:00',
        parentColumn: oldDateElement,
        top: 275,
        height: 60,
        modified: false
    },
    
    // 變更後狀態
    next: {
        date: '2026-07-29',
        startTime: '10:00',
        endTime: '12:00',
        parentColumn: newDateElement,
        top: 305,
        height: 60,
        modified: true
    }
};
```

#### 2. 操作歷程寫入觸發時機
- 當滑鼠按下 (`pointerdown`) 時，記錄 `prev` 狀態。
- 當滑鼠放開 (`pointerup`) 時，若位置/時間有變動，生成 `next` 狀態並 `undoStack.push(actionRecord)`。

#### 3. 鍵盤事件監聽 (Keyboard Shortcut)
```javascript
window.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'z') {
        // 防止瀏覽器預設復原行為
        e.preventDefault();
        undoLastAction();
    }
});

function undoLastAction() {
    if (undoStack.length === 0) {
        console.log("已無可復原的操作");
        return;
    }
    
    const lastAction = undoStack.pop();
    const { element, prev } = lastAction;
    
    // 1. 還原 DOM 容器位置 (若有跨天)
    if (element.parentElement !== prev.parentColumn) {
        prev.parentColumn.appendChild(element);
    }
    
    // 2. 還原尺寸與位置
    element.style.top = `${prev.top}px`;
    element.style.height = `${prev.height}px`;
    element.setAttribute('data-start', prev.startTime);
    element.setAttribute('data-end', prev.endTime);
    
    // 3. 還原標籤狀態
    if (prev.modified) {
        element.setAttribute('data-modified', 'true');
    } else {
        element.removeAttribute('data-modified');
    }
    
    // 4. 即時同步還原至表單
    syncFormWithEvent(lastAction.eventId, prev.date, prev.startTime, prev.endTime);
}
```

---

## 📅 預計實作步驟與異動檔案

1. **[MODIFY] [style.css](file:///d:/web/CALENDAR/style.css)**：
   - 增加 `.resize-handle-top`, `.resize-handle-bottom` 控制點樣式。
   - 增加 `.time-block[data-modified="true"]` 的視覺 Badge 樣式。
2. **[MODIFY] [js/calendar.js](file:///d:/web/CALENDAR/js/calendar.js)**：
   - 重構行程區塊渲染邏輯，加入雙向 Resize Handle。
   - 實作 Pointer Events 拖曳移動與頂部/底部拉伸。
   - 實作 `undoStack` 與 `Ctrl + Z` 鍵盤監聽與復原處理函數。
3. **[MODIFY] [calendar.php](file:///d:/web/CALENDAR/calendar.php)**：
   - 表單新增 Undo 復原按鈕（視需求可手動點擊或快捷鍵觸發）。
