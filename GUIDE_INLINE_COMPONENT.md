# Quick Copy-Paste Guide: Show Fetched Data Directly in the View

If a CodeCheck asks you to fetch data and display it in a new block/component **directly inside an existing view** (without creating a new file), follow this copy-paste workflow. 

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

## 2. The Main View (The Inline UI Box)

**File to Edit:** e.g., `app/views/student/dashboardStudent.view.php`
**Where to put it:** Wherever you want the box to appear on the page.

**Step C: Copy and Paste this entire HTML block directly into the view:**
```php
        <!-- COPY AND PASTE THIS ENTIRE COMPONENT DIRECTLY IN THE VIEW -->
        <div class="custom-widget" style="padding: 15px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; margin-bottom: 20px;">
            <!-- CHANGE THE HEADING -->
            <h4 style="margin: 0; color: #166534; font-size: 14px;">My New Component</h4>
            
            <p style="margin: 5px 0 0 0; font-size: 18px; font-weight: bold; color: #15803d;">
                <!-- CHANGE the variable name to match what you put in the Controller array -->
                <?= htmlspecialchars($myFetchedData ?? 'Empty') ?>
            </p>
        </div>
        <!-- --------------------------------------------------------- -->
```

### Done!
It's exactly the same process, except you paste the HTML block directly into the view instead of placing it in a separate PHP file and using `require`.
