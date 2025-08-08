<?php
// File: send_notification.php
include('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $notification_text = $_POST['notification_text'];
        $child_id = $_POST['child_id'];

        // Insert the notification into the database
        $stmt = $conn->prepare("INSERT INTO notifications (child_id, notification_text) VALUES (:child_id, :notification_text)");
        $stmt->bindValue(':child_id', $child_id, PDO::PARAM_INT);
        $stmt->bindValue(':notification_text', $notification_text);

        if ($stmt->execute()) {
            // Redirect back to the view_details page with a success message
            header("Location: view_details.php?id=$child_id&success=1");
            exit();
        } else {
            echo "Failed to send notification.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
