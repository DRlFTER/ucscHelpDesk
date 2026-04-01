<?php

class Attachment extends Model
{
    /**
     * Add a new attachment record to the database
     */
    public function insert($data)
    {
        $query = "INSERT INTO attachments (entity_type, entity_id, file_name, file_path, file_type, file_size, uploaded_by) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param(
            "sissiii",
            $data['entity_type'],
            $data['entity_id'],
            $data['file_name'],
            $data['file_path'],
            $data['file_type'],
            $data['file_size'],
            $data['uploaded_by']
        );
        
        if ($stmt->execute()) {
            return $stmt->insert_id;
        }
        return false;
    }

    /**
     * Retrieve attachments for a specific entity
     */
    public function getByEntity($entity_type, $entity_id)
    {
        $query = "SELECT * FROM attachments WHERE entity_type = ? AND entity_id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("si", $entity_type, $entity_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $attachments = [];
        while ($row = $result->fetch_assoc()) {
            $attachments[] = $row;
        }
        return $attachments;
    }

    /**
     * Delete an attachment from DB (File should be deleted separately)
     */
    public function delete($id)
    {
        $query = "DELETE FROM attachments WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    /**
     * Optional: Get attachment by ID (Useful if you need file path to delete the physical file)
     */
    public function getById($id)
    {
        $query = "SELECT * FROM attachments WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}