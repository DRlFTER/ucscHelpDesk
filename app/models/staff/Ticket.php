<?php
  require_once __DIR__ . '/../core/config.php';

  class Ticket {
      private static function getConnection() {
          $conn = new mysqli(DBHOST, DBUSER, DBPASSWORD, DBNAME, DBPORT);
          if ($conn->connect_error) {
              die("DB Connection failed: " . $conn->connect_error);
          }
          return $conn;
      }

      public static function getAllTickets() {
          $conn = self::getConnection();
          $sql = "SELECT ticket_id, created_at, title, student_name, category, status, priority, meeting_requested
                  FROM tickets
                  ORDER BY created_at DESC";
          $result = $conn->query($sql);
          $tickets = [];
          if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                  $tickets[] = $row;
              }
          }
          $conn->close();
          return $tickets;
      }

      public static function getTicketById($ticket_id) {
          $conn = self::getConnection();
          $sql = "SELECT ticket_id, created_at, title, student_name, category, status, priority, meeting_requested
                  FROM tickets
                  WHERE ticket_id = ?";
          $stmt = $conn->prepare($sql);
          $stmt->bind_param("i", $ticket_id);
          $stmt->execute();
          $result = $stmt->get_result();
          $ticket = $result->fetch_assoc();
          $stmt->close();
          $conn->close();
          return $ticket;
      }
  }
  ?>