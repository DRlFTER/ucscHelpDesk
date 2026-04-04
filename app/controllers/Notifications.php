<?php

class Notifications extends Controller
{
    /**
     * GET /notifications — full fetch of recent notifications + unread count.
     */
    public function index()
    {
        header('Content-Type: application/json');

        if (empty($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Not authenticated']);
            return;
        }

        $userId = (int)$_SESSION['user']['u_id'];

        try {
            $model = $this->model('Notification');
            $notifications = $model->getNotifications($userId, 30);
            $unreadCount = $model->getUnreadCount($userId);

            $role = strtolower($_SESSION['user']['role'] ?? 'student');

            $out = [];
            foreach ($notifications as $n) {
                $out[] = self::formatNotification($n, $role);
            }

            echo json_encode([
                'notifications' => $out,
                'unreadCount' => $unreadCount,
                'serverTime' => date('Y-m-d H:i:s'),
            ]);
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch notifications']);
        }
    }

    /**
     * GET /notifications/poll?since=YYYY-MM-DD+HH:MM:SS
     * Lightweight endpoint for periodic polling.
     */
    public function poll()
    {
        header('Content-Type: application/json');

        if (empty($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Not authenticated']);
            return;
        }

        $userId = (int)$_SESSION['user']['u_id'];
        $since = isset($_GET['since']) ? trim((string)$_GET['since']) : '';

        try {
            $model = $this->model('Notification');

            if ($since !== '') {
                $newNotifs = $model->getNewSince($userId, $since);
            } else {
                $newNotifs = $model->getNotifications($userId, 30);
            }

            $unreadCount = $model->getUnreadCount($userId);
            $role = strtolower($_SESSION['user']['role'] ?? 'student');

            $out = [];
            foreach ($newNotifs as $n) {
                $out[] = self::formatNotification($n, $role);
            }

            echo json_encode([
                'notifications' => $out,
                'unreadCount' => $unreadCount,
                'serverTime' => date('Y-m-d H:i:s'),
            ]);
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Poll failed']);
        }
    }

    /**
     * POST /notifications/markRead  body: { id: N }
     */
    public function markRead()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        if (empty($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Not authenticated']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $notifId = isset($input['id']) ? (int)$input['id'] : 0;
        if ($notifId <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing notification id']);
            return;
        }

        $userId = (int)$_SESSION['user']['u_id'];

        try {
            $model = $this->model('Notification');
            $model->markAsRead($notifId, $userId);
            $unreadCount = $model->getUnreadCount($userId);
            echo json_encode(['success' => true, 'unreadCount' => $unreadCount]);
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to mark as read']);
        }
    }

    /**
     * POST /notifications/markAllRead
     */
    public function markAllRead()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        if (empty($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Not authenticated']);
            return;
        }

        $userId = (int)$_SESSION['user']['u_id'];

        try {
            $model = $this->model('Notification');
            $model->markAllRead($userId);
            echo json_encode(['success' => true, 'unreadCount' => 0]);
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to mark all as read']);
        }
    }

    /**
     * Format a notification row for JSON output.
     */
    private static function formatNotification(array $n, string $role): array
    {
        $createdAt = $n['created_at'] ?? '';
        $timeAgo = self::timeAgo($createdAt);

        // Build a URL based on entity type and role
        $url = null;
        $entityType = $n['entity_type'] ?? '';
        $entityId = (int)($n['entity_id'] ?? 0);

        if ($entityType === 'ticket' && $entityId > 0) {
            $url = '/' . $role . '/ticketFull?id=' . $entityId;
        }

        return [
            'id' => (int)$n['notif_id'],
            'type' => $n['type'] ?? '',
            'message' => $n['message'] ?? '',
            'isRead' => (bool)($n['is_read'] ?? false),
            'createdAt' => $createdAt,
            'timeAgo' => $timeAgo,
            'url' => $url,
            'entityType' => $entityType,
            'entityId' => $entityId,
        ];
    }

    /**
     * Simple time-ago formatter.
     */
    private static function timeAgo(string $datetime): string
    {
        if (!$datetime) return '';
        $ts = strtotime($datetime);
        if ($ts === false) return '';

        $diff = time() - $ts;
        if ($diff < 0) return 'just now';
        if ($diff < 60) return 'just now';
        if ($diff < 3600) return floor($diff / 60) . 'm ago';
        if ($diff < 86400) return floor($diff / 3600) . 'h ago';
        if ($diff < 604800) return floor($diff / 86400) . 'd ago';
        return date('M d', $ts);
    }
}
