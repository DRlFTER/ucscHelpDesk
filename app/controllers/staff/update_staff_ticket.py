with open('/Applications/XAMPP/xamppfiles/htdocs/ucscHelpDesk/app/controllers/staff/Staff.php', 'r') as f:
    text = f.read()

# Add boolean flags to $out in ticketData
out_replacement = r"""            'type' => (string)($row['t_type'] ?? 'public'),
            'assigned_to' => isset($row['assigned_to']) ? (int)$row['assigned_to'] : null,
            'isAssignedToMe' => (isset($row['assigned_to']) && $row['assigned_to'] == $_SESSION['user']['u_id']),
            'isPending' => ($statusRaw === 'pending')"""
text = text.replace("'type' => (string)($row['t_type'] ?? 'public')", out_replacement)

# Add ticketAssign, ticketForward, staffMembers endpoints
new_methods = """
    public function ticketAssign()
    {
        $this->requireLogin('staff');
        header('Content-Type: application/json');
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if (!$id) {
            http_response_code(400); echo json_encode(['error' => 'Missing id']); exit;
        }
        
        require_once __DIR__ . '/../../models/staff/Ticket.php';
        $model = new StaffTicket();
        try {
            $ticket = $model->getTicketById($id);
            if ($ticket && strtolower($ticket['status']) === 'pending') {
                $current_staff_id = (int)$_SESSION['user']['u_id'];
                $ok = $model->assignToStaff($id, $current_staff_id);
                $ok2 = $model->setTicketupdateTimeline($id);
                if ($ok && $ok2) {
                    $model->setTicketLevel($id, $current_staff_id, $model->getStaffLevel($current_staff_id));
                    echo json_encode(['success' => true]);
                } else {
                    http_response_code(500); echo json_encode(['error' => 'Failed to assign']);
                }
            } else {
                http_response_code(400); echo json_encode(['error' => 'Ticket not pending']);
            }
        } catch (Throwable $e) {
            http_response_code(500); echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    public function ticketForward()
    {
        $this->requireLogin('staff');
        header('Content-Type: application/json');
        
        $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = isset($data['id']) ? (int)$data['id'] : ($_GET['id'] ?? 0);
        $forward_to = isset($data['forward_to']) ? (int)$data['forward_to'] : 0;
        
        if (!$id || !$forward_to) {
            http_response_code(400); echo json_encode(['error' => 'Missing id or target staff']); exit;
        }
        
        require_once __DIR__ . '/../../models/staff/Ticket.php';
        $model = new StaffTicket();
        try {
            $ticket = $model->getTicketById($id);
            if ($ticket && $ticket['assigned_to'] == $_SESSION['user']['u_id']) {
                $ok = $model->forwardTicket($id, $forward_to);
                if ($ok) {
                    $model->setTicketLevel($id, $forward_to, $model->getStaffLevel($forward_to));
                    echo json_encode(['success' => true]);
                } else {
                    http_response_code(500); echo json_encode(['error' => 'Failed to forward']);
                }
            } else {
                http_response_code(403); echo json_encode(['error' => 'Not assigned to you']);
            }
        } catch (Throwable $e) {
            http_response_code(500); echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    public function staffMembersList()
    {
        $this->requireLogin('staff');
        header('Content-Type: application/json');
        
        require_once __DIR__ . '/../../models/staff/Ticket.php';
        $model = new StaffTicket();
        try {
            $members = $model->getStaffMembers();
            echo json_encode(['success' => true, 'data' => $members]);
        } catch (Throwable $e) {
            http_response_code(500); echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

"""

# Insert right before ticketResolve
parts = text.split('    public function ticketResolve()')
new_text = parts[0] + new_methods + '    public function ticketResolve()' + parts[1]

# Also add ticketFull endpoint rendering
ticket_full_method = """
    public function ticketFull() {
        $this->requireLogin('staff');
        $headContent = '<link rel="stylesheet" href="/css/ticketFull/ticketFull.css"/>';
        $this->view('ticketFull', [
            'title' => 'Ticket Details',
            'head' => $headContent,
            'role' => 'staff',
        ]);
    }
"""

parts2 = new_text.split('    public function tickets()')
new_text = parts2[0] + ticket_full_method + '\n    public function tickets()' + parts2[1]

with open('/Applications/XAMPP/xamppfiles/htdocs/ucscHelpDesk/app/controllers/staff/Staff.php', 'w') as f:
    f.write(new_text)

print("Staff.php updated successfully!")
