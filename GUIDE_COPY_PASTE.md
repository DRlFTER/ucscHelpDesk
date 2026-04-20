# How to Add a New Field to a Form (Quick Copy-Paste Guide)

In a practical exam like a CodeCheck, writing code from scratch takes too much time. The most efficient way to add a new feature (like a `"flag"` input field) is by finding a similar existing input and imitating it.

Here is a step-by-step guide on how to copy, paste, and adapt code across the four main layers of the MVC pattern: **View (UI) -> Controller (Logic) -> Model (Database) -> Frontend JS (Display)**.

---

## 1. The View (Adding the Input Field)

**File to Edit:** `app/views/student/newTicketStudent.view.php`

**What to look for:** Look for an existing text input, like the "Title" field.

**Original Code snippet to copy:**
```html
<div class="field">
    <label class="label" for="title">Title/Subject <span style="color:#ef4444; margin-left:2px">*</span></label>
    <input id="title" name="title" type="text" placeholder="Briefly describe your issue..." required>
</div>
```

**How to adapt it (What you write):**
*   **What you must change manually:** The `for`, `id`, `name`, and label text to match your new field (e.g., `flag`).
*   **What you can keep:** The HTML structure, CSS classes (`class="field"`, `class="label"`), and input type (`type="text"`).

**Final code snippet:**
```html
<div class="field">
    <!-- Changed 'title' to 'flag'. Removed the required * span if it's optional -->
    <label class="label" for="flag">Flag <span style="font-size:12px; color:#888;">(Optional)</span></label>
    <!-- Changed id, name, and placeholder -->
    <input id="flag" name="flag" type="text" placeholder="Add a custom flag...">
</div>
```

---

## 2. The Controller (Receiving and Preparing Data)

**File to Edit:** `app/controllers/student/Student.php`

**What to look for:** Find the controller method handling the form submission. Look for `$_POST`.

**Original Code snippet to copy:**
```php
$title = trim($_POST['title'] ?? '');
$category = trim($_POST['category'] ?? '');
```

**How to adapt it:**
*   Copy one of those lines and change the variable name and the `$_POST` key to match your new input field's `name` attribute.

**What you write:**
```php
$flag = trim($_POST['flag'] ?? '');
```

**Adding to the Model array:**
Further down in the controller, it passes an array to the Model's `create()` method. Add your new variable inside this array.
```php
$ticketId = $ticketModel->create([
    'title' => $title,
    'u_id' => (int)($_SESSION['user']['u_id'] ?? 0),
    // ... other fields
    'flag' => $flag, // <- You manually add this line mapping the DB column to your variable
]);
```

---

## 3. The Model (Saving to the Database)

**File to Edit:** `app/models/student/Ticket.php`

**What to look for:** Find the `create()` method that handles the database insertion (`INSERT INTO`).

**How to adapt it:**
There are four places you *must* modify manually. You cannot just copy-paste this blindly because prepared statements require exact matching counts.

1.  **Add the variable:** Read the new property array passed from the controller.
    ```php
    $flag = $data['flag'] ?? null;
    ```
2.  **Update the SQL String:** Add your new column name, and add ONE extra `?` placeholder in the `VALUES`.
    ```sql
    /* Before: */ "INSERT INTO tickets (created_at, title, u_id, status, priority, description, meeting_requested, division, t_type) VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    /* After: */  "INSERT INTO tickets (created_at, title, flag, u_id, status, priority, description, meeting_requested, division, t_type) VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    ```
3.  **Update `bind_param` string:** Add a character to represent the data type. `s` for string, `i` for integer. Since `flag` is a string, add an `s`.
    ```php
    // Before: 'sissssis'
    // After: 'ssissssis' -> We added an 's' at the second position (matching where 'flag' is in our SQL)
    ```
4.  **Update `bind_param` variables:** Add the variable in the exact same order as you placed it in the SQL string.
    ```php
    $stmt->bind_param('ssissssis', $title, $flag, $u_id, $status /* etc */);
    ```

---

## 4. Reading Data Back (Controller -> JS)

### Step 4A: Fetching the data in the Controller
**File to Edit:** `app/controllers/student/Student.php` (Look for the `ticketsData()` method that powers the Tickets page).

**How to adapt it:**
1.  **Update the `SELECT` query:** Just add the column name (`t.flag,`) to the SELECT list.
    ```sql
    $sql = "SELECT t.ticket_id, t.created_at, t.title, t.flag, d.name AS division_name...
    ```
2.  **Process the row array:** Further down where the rows are mapped to the JSON output, add your field.
    ```php
    $out[] = [
        'id' => isset($r['ticket_id']) ? (int)$r['ticket_id'] : null,
        'title' => (string)($r['title'] ?? ''),
        // Copy a string line above and change key/value
        'flag' => (string)($r['flag'] ?? ''), 
    ];
    ```

### Step 4B: Displaying the data in Javascript
**File to Edit:** `public/js/tickets/tickets.js` (Look for the `renderTickets()` method).

**What to look for:** Look for how other dynamic UI elements are injected into the HTML string, (e.g., `visibilityHtml` or `overdueBadge`).

**How to adapt it:**
1.  Create an empty string variable.
2.  Add an `if` block checking if the property from your JSON exists (`t.flag`).
3.  If it exists, reassign the string variable to some HTML markup.
4.  Inject the variable into the main template literal.

**What you write:**
```javascript
// Step 1 & 2
let flagHtml = '';
if (t.flag) {
    // Step 3 (Safely escape data to avoid XSS)
    flagHtml = `<div class="status" style="background:#e0e7ff; color:#0369a1; border:1px solid #bae6fd;">Flag: ${esc(t.flag)}</div>`;
}

const inner = `
<div class="ticketRow1">
    <div class="ticketName">...</div>
    <div style="display:flex; gap:10px; align-items:center;">
        <!-- Step 4 -->
        ${flagHtml} 
        ${visibilityHtml}
        <div class="status ${status.cls}">${esc(status.label)}</div>
    </div>
</div>
`;
```