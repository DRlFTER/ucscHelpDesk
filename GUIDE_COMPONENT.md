# Quick Copy-Paste Guide: Create a Component to Show Fetched Data

If a CodeCheck asks you to "fetch a specific value from the database and display it in a new component," follow this exact copy-paste workflow. 

---

## 1. The Controller (Fetch Data & Pass to View)

**File to Edit:** e.g., `app/controllers/student/Student.php`
**Where to put it:** Inside the method loading the page (e.g., `public function dashboard()`), right **before** the `$this->view(...)` call.

**Step A: Copy and Paste this fetch block**
```php
        // --- 1. COPY PASTE THIS DATABASE FETCH ---
        $db = Database::getInstance();
        $uId = (int)($_SESSION['user']['u_id'] ?? 0);
        
        // MANUALLY CHANGE THIS SQL QUERY to whatever they ask for
        $sql = "SELECT title FROM tickets WHERE u_id = $uId ORDER BY created_at DESC LIMIT 1";
        $res = $db->query($sql);
        
        // MANUALLY CHANGE THIS VARIABLE NAME
        $myFetchedData = "No data found"; 
        
        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            // MANUALLY CHANGE 'title' to match your SQL select column
            $myFetchedData = $row['title']; 
            $res->free();
        }
        // ----------------------------------------
```

**Step B: Add your variable to the View Array**
Scroll down a few lines to where `$this->view(...)` is called.

**What to modify:** Add your new variable inside the array.
```php
        $this->view('dashboardStudent', [
            'title' => 'Student Dashboard',
            // COPY THIS LINE and change the variable name
            'myFetchedData' => $myFetchedData, 
            // ... (other existing variables)
        ]);
```

---

## 2. The Component (The UI Box)

**File to Create:** Create a brand new file in: `app/views/components/myDynamicWidget.php`
*(Note: Change `myDynamicWidget` to whatever name makes sense).*

**Step C: Copy and Paste this entire HTML/PHP block into your new file:**
```php
<!-- COPY AND PASTE THIS ENTIRE COMPONENT -->
<div class="custom-widget" style="padding: 15px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; margin-bottom: 20px;">
    <!-- CHANGE THE HEADING -->
    <h4 style="margin: 0; color: #166534; font-size: 14px;">My New Component</h4>
    
    <p style="margin: 5px 0 0 0; font-size: 18px; font-weight: bold; color: #15803d;">
        <!-- CHANGE the variable name to match what you put in the Controller array -->
        <?= htmlspecialchars($myFetchedData ?? 'Empty') ?>
    </p>
</div>
```

---

## 3. The Main View (Injecting the Component)

**File to Edit:** e.g., `app/views/student/dashboardStudent.view.php`
**Where to put it:** Wherever you want the box to appear on the page (usually right after the `<div class="ticketHeader">` or similar main container).

**Step D: Copy and Paste this PHP require line:**
```php
        <!-- COPY AND PASTE THIS LINE -->
        <!-- MANUALLY CHANGE the filename to match the file you created in Step 2 -->
        <?php require __DIR__ . '/../components/myDynamicWidget.php'; ?>
```

### Done!
That's all it takes. The controller grabs the DB data, passes it to the view, and the view inserts your new component file which safely prints the variable.
