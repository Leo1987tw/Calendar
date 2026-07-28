# 互動式萬年曆 — 整體性優化分析報告

> 分析時間：2026-07-28  
> 版本：v2.0 (全面優化版)

---

## 一、專案結構總覽

```
CALENDAR/
├── index.php                  # 應用程式入口，包含 HTML 框架與標頭
├── calendar.php               # 月曆主頁面 (PHP 渲染日期格子 + 行程表單)
├── db.php                     # 資料庫 PDO 封裝類別 (DB class)
├── db_config.php              # PDO 連線設定
├── api_add_event.php          # RESTful API：新增行程
├── api_edit_event.php         # RESTful API：單筆編輯行程
├── api_delete_event.php       # RESTful API：軟刪除行程
├── api_get_events.php         # RESTful API：取得所有行程
├── api_batch_save_events.php  # RESTful API：批次儲存已修改行程 (DB Transaction)
├── style.css                  # 主要樣式表 (RWD 玻璃擬態設計)
├── js/calendar.js             # 前端邏輯 (拖曳、復原、Toast、CRUD、背景切換)
├── images/                    # 12 張月份背景圖
├── doc/                       # 技術文件資料夾
└── calendar.sql               # 資料庫結構
```

---

## 二、優化項目清單

### 🐛 Bug 修正

| # | 問題位置 | 問題描述 | 修正方式 |
|---|----------|----------|----------|
| 1 | `calendar.php` L47 | `data-id` 使用未補零的 `$thisMonth`，導致日期格式為 `2026-7-01` 而非 `2026-07-01`，造成 JS `querySelector` 查找行程時失敗 | 新增 `$thisMonthPadded = sprintf('%02d', $thisMonth)` 並替換所有 `$thisMonth` 於 `data-id` 中 |
| 2 | `api_get_events.php` | 事件未排序，前端接收順序不定 | 加入 `ORDER BY event_date ASC, start_time ASC` |
| 3 | `addEvent()` JS | 未驗證「結束時間早於開始時間」的非法輸入 | 加入 `startTime >= endTime` 的時間合法性驗證 |

---

### 🔐 資安優化

| # | 問題位置 | 問題描述 | 修正方式 |
|---|----------|----------|----------|
| 4 | `api_get_events.php` | `Access-Control-Allow-Origin: *` 允許任何跨域網站呼叫 API，存在 CSRF 與資料外洩風險 | 移除不安全的萬用 CORS 標頭，限制為同源請求 |
| 5 | `db.php` `all()`、`count()` | 支援傳入原始 SQL 字串（`$args[0]` 為字串時直接拼接），允許潛在 SQL 注入 | 已記錄為 **技術債**，建議未來重構改為完整的 QueryBuilder 模式 |

---

### ✨ UX 使用者體驗優化

| # | 改動項目 | 改動描述 |
|---|----------|----------|
| 6 | **Toast 通知系統** | 全面取代 `alert()` 為非阻塞式 Toast 通知，具備滑入動畫、4 種類型 (success / error / warning / info)、3.5 秒自動消失 |
| 7 | **頁面標頭** | 新增 `<header class="app-header">` 包含 `🗓️ 互動式萬年曆` 主標題與 `Interactive Event Management System` 副標題，採 glassmorphism 毛玻璃效果，position: sticky 隨捲動固定頂部 |
| 8 | **手機標頭適配** | `≤640px` 時隱藏副標題，縮小 padding，保持行動端整潔 |
| 9 | **Toast 手機版** | 手機版 Toast 延伸至全寬（`left: 16px; right: 16px`），避免文字截斷 |

---

### ⚡ 效能優化建議 (尚未實作，列為待辦)

| # | 問題 | 建議方案 |
|---|------|----------|
| 10 | `renderEventsToCalendar()` 每次操作後全量 `fetch` | 改為差異更新 (Diff Patch)：保留已載入的行程 Map，新增/修改/刪除時只更新對應 DOM 節點 |
| 11 | `style.css` 無 `@layer` 分層 | 建議改用 `@layer base, components, utilities` 提高 CSS 可維護性 |
| 12 | 圖片未使用 WebP/AVIF 格式 | 建議將 `images/month-*.jpg` 轉換為 WebP，可節省 50~70% 傳輸體積 |
| 13 | jQuery 3.7.1 CDN 無 SRI Hash 更新 | 已有 integrity 屬性，建議定期審查是否為最新版本 |

---

### ♿ 無障礙設計建議 (Accessibility / a11y)

| # | 問題 | 建議方案 |
|---|------|----------|
| 14 | 月曆日期格子缺少 `role="gridcell"` 與 `aria-label` | 增加 `role="grid"`, `role="gridcell"`, `aria-label="YYYY年MM月DD日"` |
| 15 | Toast 通知缺少 `aria-live` | 在 `#toast-container` 加上 `aria-live="polite"` 讓螢幕閱讀器播報 |
| 16 | 按鈕缺少 `aria-label` 說明 | 為表情符號按鈕加上 `aria-label="新增行程"` 等屬性 |
| 17 | 色彩對比 | 部分次要文字色 (`#64748b`) 在白底下對比率為 4.6:1，WCAG AA 標準通過，建議朝 AAA (7:1) 努力 |

---

## 三、系統架構分析圖

```
┌─────────────────────────────────────────────────────────┐
│                     index.php (入口)                     │
│  <header>互動式萬年曆</header>                           │
│  <main> include calendar.php </main>                    │
└─────────────────┬───────────────────────────────────────┘
                  │
        ┌─────────▼──────────┐
        │    calendar.php    │ (PHP 渲染月曆格子 + 行程表單)
        │  - $thisMonthPadded│ (月份補零 Bug Fix ✅)
        │  - DB::all()       │ (撈取 types 清單)
        └─────────┬──────────┘
                  │ include
        ┌─────────▼──────────┐
        │       db.php       │ (PDO 封裝類 + Prepared Statements)
        │  - DB::all()       │
        │  - DB::save()      │
        │  - DB::softDel()   │
        └─────────┬──────────┘
                  │ $pdo
        ┌─────────▼──────────┐
        │   db_config.php    │ (PDO 連線)
        └────────────────────┘

前端 JavaScript 流程：
    DOMContentLoaded
        └── updateMonthBackground(month)    ← 設定月份背景圖
        └── keydown: Ctrl+Z → undoLastAction()
        └── click .date → renderEventsToCalendar()
                └── fetch api_get_events.php
                    └── bindPointerEvents(element, id)
                        ├── pointerdown → pushUndoRecord()
                        ├── pointermove → 即時位移/拉伸 + syncFormWithEvent()
                        └── pointerup   → 記錄最終狀態

    saveAllModifiedEvents()
        └── POST api_batch_save_events.php  ← DB Transaction
            └── showToast('success')        ← Toast 通知 ✅ (已取代 alert)
```

---

## 四、資料庫結構

```sql
-- events 資料表
CREATE TABLE events (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  event_date       DATE,
  start_time       TIME,
  end_time         TIME,
  type             VARCHAR(255),
  title            VARCHAR(255),
  description      TEXT,
  color            VARCHAR(20),
  background_color VARCHAR(20),
  border_color     VARCHAR(20),
  created_at       DATETIME,
  updated_at       DATETIME,
  deleted_at       DATETIME   -- 軟刪除欄位 (Soft Delete)
);

-- types 資料表 (行程類型)
CREATE TABLE types (
  id   INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255)
);
```

---

## 五、改進優先度建議

| 優先度 | 項目 | 說明 |
|--------|------|------|
| 🔴 高 | 修復 `db.php` SQL 字串拼接 | 潛在注入風險 (技術債) |
| 🔴 高 | 加入 CSRF Token 驗證 | 目前所有 API 無 CSRF 防護 |
| 🟡 中 | `renderEventsToCalendar` 差異更新 | 目前每次操作全量 fetch 影響效能 |
| 🟡 中 | 圖片格式轉 WebP | 12 張背景圖節省頻寬 |
| 🟢 低 | 增加 `aria-*` 無障礙屬性 | 提升螢幕閱讀器相容性 |
| 🟢 低 | `@layer` CSS 分層 | 提高 CSS 可維護性 |

---

## 六、本次優化異動檔案清單

| 檔案 | 異動類型 | 說明 |
|------|----------|------|
| [index.php](file:///d:/web/CALENDAR/index.php) | 新增 | 加入 `<header class="app-header">` 互動式萬年曆標頭 |
| [calendar.php](file:///d:/web/CALENDAR/calendar.php) | Bug Fix | 新增 `$thisMonthPadded` 修正 `data-id` 月份不補零問題 |
| [api_get_events.php](file:///d:/web/CALENDAR/api_get_events.php) | 安全 + 優化 | 移除 CORS 萬用字元；加入 `ORDER BY event_date, start_time` |
| [js/calendar.js](file:///d:/web/CALENDAR/js/calendar.js) | 功能新增 + 重構 | 新增 `showToast()` 取代所有 `alert()`；加入 `startTime >= endTime` 驗證 |
| [style.css](file:///d:/web/CALENDAR/style.css) | 新增 | 新增 App Header CSS；加入 Toast 通知樣式系統；手機版微調 |
