<?php
/**
 * Counselor Forum Model
 * Location: models/counselor/Forum.php
 */

// Make sure Database class is available
if (!class_exists('Database')) {
    require_once __DIR__ . '/../../core/Database.php';
}

class CounselorForumModel
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get all forum topics with filters
     * 
     * @param string|null $category Category to filter by
     * @param string $sortBy Sort order (recent, popular, unanswered)
     * @param string|null $search Search term
     * @return array Array of topics
     */
    public function getForumTopics($category = null, $sortBy = 'recent', $search = null)
    {
        $sql = "SELECT 
                    ft.id,
                    ft.title,
                    ft.content,
                    ft.category,
                    ft.is_pinned,
                    ft.view_count,
                    ft.created_at,
                    ft.author_id,
                    COALESCE(u.name, 'Anonymous') as author_name,
                    COALESCE(u.role, 'user') as author_role,
                    (SELECT COUNT(*) FROM forum_replies fr WHERE fr.topic_id = ft.id) as reply_count
                FROM forum_topics ft
                LEFT JOIN users u ON ft.author_id = u.u_id
                WHERE 1=1";
        
        $params = [];
        $types = '';
        
        if ($category) {
            $sql .= " AND ft.category = ?";
            $params[] = $category;
            $types .= 's';
        }
        
        if ($search) {
            $sql .= " AND (ft.title LIKE ? OR ft.content LIKE ?)";
            $searchTerm = '%' . $search . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= 'ss';
        }
        
        // Sort logic
        switch ($sortBy) {
            case 'popular':
                $sql .= " ORDER BY ft.view_count DESC, ft.created_at DESC";
                break;
            case 'unanswered':
                $sql .= " HAVING reply_count = 0 ORDER BY ft.created_at DESC";
                break;
            default: // recent
                $sql .= " ORDER BY ft.is_pinned DESC, ft.created_at DESC";
        }
        
        if ($stmt = $this->db->prepare($sql)) {
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $topics = [];
            while ($row = $result->fetch_assoc()) {
                $topics[] = $row;
            }
            $stmt->close();
            return $topics;
        }
        
        return [];
    }
    
    /**
     * Get single topic by ID
     * 
     * @param int $topicId Topic ID
     * @return array|null Topic data or null if not found
     */
    public function getTopicById($topicId)
    {
        $sql = "SELECT 
                    ft.id,
                    ft.title,
                    ft.content,
                    ft.category,
                    ft.is_pinned,
                    ft.view_count,
                    ft.created_at,
                    ft.author_id,
                    COALESCE(u.name, 'Anonymous') as author_name,
                    COALESCE(u.role, 'user') as author_role
                FROM forum_topics ft
                LEFT JOIN users u ON ft.author_id = u.u_id
                WHERE ft.id = ?";
        
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param('i', $topicId);
            $stmt->execute();
            $result = $stmt->get_result();
            $topic = $result->fetch_assoc();
            $stmt->close();
            return $topic ?: null;
        }
        
        return null;
    }
    
    /**
     * Get replies for a topic
     * 
     * @param int $topicId Topic ID
     * @return array Array of replies
     */
    public function getTopicReplies($topicId)
    {
        $sql = "SELECT 
                    fr.id,
                    fr.content,
                    fr.created_at,
                    fr.author_id,
                    COALESCE(u.name, 'Anonymous') as author_name,
                    COALESCE(u.role, 'user') as author_role
                FROM forum_replies fr
                LEFT JOIN users u ON fr.author_id = u.u_id
                WHERE fr.topic_id = ?
                ORDER BY fr.created_at ASC";
        
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param('i', $topicId);
            $stmt->execute();
            $result = $stmt->get_result();
            $replies = [];
            while ($row = $result->fetch_assoc()) {
                $replies[] = $row;
            }
            $stmt->close();
            return $replies;
        }
        
        return [];
    }
    
    /**
     * Create new topic
     * 
     * @param string $title Topic title
     * @param string $content Topic content
     * @param string $category Topic category
     * @param bool $isPinned Whether topic is pinned
     * @param int $authorId Author user ID
     * @return int|null New topic ID or null on failure
     */
    public function createTopic($title, $content, $category, $isPinned, $authorId)
    {
        $sql = "INSERT INTO forum_topics (title, content, category, is_pinned, author_id, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        
        if ($stmt = $this->db->prepare($sql)) {
            $pinned = $isPinned ? 1 : 0;
            $stmt->bind_param('sssii', $title, $content, $category, $pinned, $authorId);
            
            if ($stmt->execute()) {
                $topicId = $stmt->insert_id;
                $stmt->close();
                return $topicId;
            }
            $stmt->close();
        }
        
        return null;
    }
    
    /**
     * Create reply to topic
     * 
     * @param int $topicId Topic ID
     * @param string $content Reply content
     * @param int $authorId Author user ID
     * @return int|null New reply ID or null on failure
     */
    public function createReply($topicId, $content, $authorId)
    {
        $sql = "INSERT INTO forum_replies (topic_id, content, author_id, created_at) 
                VALUES (?, ?, ?, NOW())";
        
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param('isi', $topicId, $content, $authorId);
            
            if ($stmt->execute()) {
                $replyId = $stmt->insert_id;
                $stmt->close();
                return $replyId;
            }
            $stmt->close();
        }
        
        return null;
    }
    
    /**
     * Delete reply (only by author)
     * 
     * @param int $replyId Reply ID
     * @param int $authorId Author user ID (for verification)
     * @return bool True if deleted, false otherwise
     */
    public function deleteReply($replyId, $authorId)
    {
        $sql = "DELETE FROM forum_replies WHERE id = ? AND author_id = ?";
        
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param('ii', $replyId, $authorId);
            $success = $stmt->execute() && $stmt->affected_rows > 0;
            $stmt->close();
            return $success;
        }
        
        return false;
    }
    
    /**
     * Increment view count for a topic
     * 
     * @param int $topicId Topic ID
     * @return void
     */
    public function incrementViewCount($topicId)
    {
        $sql = "UPDATE forum_topics SET view_count = view_count + 1 WHERE id = ?";
        
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param('i', $topicId);
            $stmt->execute();
            $stmt->close();
        }
    }
}