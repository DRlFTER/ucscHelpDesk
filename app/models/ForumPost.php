<?php




class ForumPost extends Model
{
    


    public function getPost($postId)
    {
        $query = "SELECT * FROM forum_q WHERE q_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    



    public function updatePost($postId, $userId, $title, $description)
    {
        $query = "UPDATE forum_q SET title = ?, description = ? WHERE q_id = ? AND u_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ssii", $title, $description, $postId, $userId);
        return $stmt->execute();
    }
}
