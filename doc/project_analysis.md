# 萬年曆與行程管理系統 (Calendar) - 專案分析與改進建議報告

## 📌 一、 專案架構概覽

本專案為一個基於 **PHP (Procedural & Class OOP) + MySQL (PDO) + Vanilla JS / jQuery / CSS Grid** 所建構的動態萬年曆與行程管理 Web 應用程式。

### 檔案目錄與職責說明

| 檔案路徑 | 類型 | 功能說明 |
| :--- | :--- | :--- |
| [index.php](file:///d:/web/CALENDAR/index.php) | Web 入口 | 載入資料庫基礎庫 [db.php](file:///d:/web/CALENDAR/db.php) 與前端依賴庫（jQuery），並引入主元件 [calendar.php](file:///d:/web/CALENDAR/calendar.php)。 |
| [calendar.php](file:///d:/web/CALENDAR/calendar.php) | 頁面元件 / 邏輯 | 核心前端 UI 與 JavaScript 互動區塊。包含月曆網格計算、行程時間軸渲染、行程表單、動態視圖切換與 AJAX 行程操作。 |
| [db.php](file:///d:/web/CALENDAR/db.php) | 後端 ORM | 自訂資料庫抽象類別 `DB`。封裝常用的 `all()`, `find()`, `save()`, `del()`, `count()`, `q()` 方法，並實例化 `$Events` 與 `$Types` 物件。 |
| [db_config.php](file:///d:/web/CALENDAR/db_config.php) | 後端設定 | 設定 MySQL PDO 連線參數、錯誤顯示層級 (`display_errors`) 與異常處理機制。 |
| [api_get_events.php](file:///d:/web/CALENDAR/api_get_events.php) | REST API | 查詢未刪除 (`deleted_at IS NULL`) 的行程，回傳 JSON 格式。 |
| [api_add_event.php](file:///d:/web/CALENDAR/api_add_event.php) | REST API | 接收 POST 請求，新增行程至 `events` 資料表。 |
| [api_edit_event.php](file:///d:/web/CALENDAR/api_edit_event.php) | REST API | 接收 POST 請求，修改指定 `id` 之行程。 |
| [api_delete_event.php](file:///d:/web/CALENDAR/api_delete_event.php) | REST API | 接收 POST 請求，刪除指定 `id` 之行程。 |
| [calendar.sql](file:///d:/web/CALENDAR/calendar.sql) | 資料庫 | 資料庫與資料表 Schema 定義檔（`events`, `types`）。 |
| [style.css](file:///d:/web/CALENDAR/style.css) | 樣式表 | 定義 CSS Grid 網格佈局、展開/收合過渡動畫 (`.collapse`)、毛玻璃背景與行程卡片樣式。 |

---

## ⚙️ 二、 系統核心運作流程

1. **資料庫連線與抽象化**：
   - 透過 [db.php](file:///d:/web/CALENDAR/db.php) 的 `DB` 類別處理資料庫連線。
   - `save()` 方法可依據輸入陣列中是否包含 `id` 鍵值，自動判斷執行 `INSERT` 或 `UPDATE`。

2. **日曆網格與日期計算**：
   - 根據 URL GET 參數 `month=YYYY-MM` 計算當月起始星期、總天數與總週數。
   - 透過 PHP 迴圈產生 CSS Grid 網格，補齊上個月底與下個月初的日期格子，並透過 `data-id="YYYY-MM-DD"` 綁定每格日期。

3. **視圖轉換與視窗動畫**：
   - 當使用者點擊某一日期時，前端透過 CSS Class (`active`, `collapse`, `none`) 與 JS `setTimeout` 控制動畫過渡，將月視圖縮排隱藏，展開當週與該日的時間軸檢視區域。

4. **動態行程渲染與區塊控制**：
   - 呼叫 `renderEventsToCalendar()` 向 [api_get_events.php](file:///d:/web/CALENDAR/api_get_events.php) 讀取行程。
   - 依據行程的 `start_time` 與 `end_time` 計算持續時間，並換算為像素高度與 `top` 位移進行絕對定位。
   - 使用 HTML5 `ResizeObserver` 監聽行程區塊的高度變更，允許使用者直接在介面上調整行程時長並即時寫回表單。

---

## 💡 三、 專案改進建議

針對目前程式碼架構、安全性、效能與維護性，提出以下具體改進建議：

### 1. 🚨 資安防護 (High Priority)
* **SQL 注入攻擊 (SQL Injection)**：
  - [db.php](file:///d:/web/CALENDAR/db.php) 中的 `a2s()` 方法直接使用字串拼接的方式組裝 SQL 命令（例如 `` `$key`='$value' ``），並直接執行 `query()` 或 `exec()`。這導致惡意使用者可透過表單輸入欄位進行 SQL 注入。
  - **建議**：改用 PDO 預處理語句 (Prepared Statements) 與參數綁定 (`PDOStatement::bindValue` 或 `execute($params)`)。
* **XSS 跨站腳本攻擊 (Cross-Site Scripting)**：
  - [calendar.php](file:///d:/web/CALENDAR/calendar.php) 中直接將資料庫讀出的 `title` 與 `description` 插入至 `innerHTML`。
  - **建議**：在前端渲染動態文字時使用 `textContent` 或實作 HTML Entity 轉義函數。

### 2. 🐛 程式碼 Bug 與語法修正
* **JS 變數拼字錯誤 (Runtime Exception)**：
  - [calendar.php](file:///d:/web/CALENDAR/calendar.php) 第 459 行與 466 行存在變數拼寫錯誤：
    - `newDutationMinutes` (應為 `newDurationMinutes`)
    - `NewEndMinutes` (應為 `newEndMinutes`)
  - 此錯誤會導致使用者在拉伸行程區塊調整時間時觸發 JS `ReferenceError` 崩潰。
* **全域 `event` 物件誤用**：
  - 行程按鈕如 `onclick="addEvent()"` 在 JS 函數中直接取用全域 `event.stopPropagation()`，在某些瀏覽器環境中可能無效或引發異常。
  - **建議**：在 HTML 屬性中顯式傳入 `event`（如 `onclick="addEvent(event)"`）並在函數簽名中接收參數。

### 3. 🔄 資料庫設定與架構重構
* **重複設定與硬編碼**：
  - [db.php](file:///d:/web/CALENDAR/db.php) 註解掉了引入 [db_config.php](file:///d:/web/CALENDAR/db_config.php) 的邏輯，並直接在 `__construct()` 中硬編碼 `localhost`, `calendar`, `root`, `''` 等連線參數。
  - **建議**：統一由 [db_config.php](file:///d:/web/CALENDAR/db_config.php) 讀取設定檔或環境變數 (.env)，避免多處維護連線資訊。
* **軟刪除 (Soft Delete) 與硬刪除不一致**：
  - [api_get_events.php](file:///d:/web/CALENDAR/api_get_events.php) 查詢條件含有 `WHERE deleted_at IS NULL`。
  - 但 [api_delete_event.php](file:///d:/web/CALENDAR/api_delete_event.php) 卻調用 `$Events->del($_POST['id'])` 執行真正的物理刪除 (`DELETE FROM`)。
  - **建議**：將刪除 API 改為軟刪除更新 `UPDATE events SET deleted_at = NOW() WHERE id = ?`，以保持資料一致性與歷史紀錄復原能力。

### 4. 🌐 API 規範與 HTTP 狀態碼
* 目前 API 檔案（如 [api_add_event.php](file:///d:/web/CALENDAR/api_add_event.php)）缺少 `Content-Type: application/json` header，且在非 `POST` 請求時僅靜默回傳空內容。
* **建議**：
  - 統一設定 `header('Content-Type: application/json; charset=utf-8');`。
  - 針對錯誤請求回傳適當的 HTTP 狀態碼（如 400 Bad Request, 405 Method Not Allowed, 500 Internal Server Error）。

### 5. 🎨 前前端架構與前後端分離
* **前後端混合程式碼**：[calendar.php](file:///d:/web/CALENDAR/calendar.php) 同時包含 PHP 渲染、HTML 結構、CSS 內聯樣式以及超過 500 行的 JavaScript 邏輯。
* **建議**：
  - 將 JavaScript 邏輯抽離至獨立的 `.js` 檔（如 `js/calendar.js` 與 `js/api.js`）。
  - 將內聯 CSS (`style="..."`) 歸納回 [style.css](file:///d:/web/CALENDAR/style.css)，提高可讀性與瀏覽器快取效率。
