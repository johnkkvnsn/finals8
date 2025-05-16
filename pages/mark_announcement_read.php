<?php
session_start();
require_once '../database/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

// Get announcement ID from POST data
$announcement_id = isset($_POST['announcement_id']) ? (int)$_POST['announcement_id'] : 0;

if ($announcement_id <= 0) {
    echo json_encode(['error' => 'Invalid announcement ID']);
    exit;
}

try {
    // Verify the announcement exists
    $stmt = $conn->prepare("SELECT id FROM announcements WHERE id = ?");
    $stmt->bind_param("i", $announcement_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Announcement not found');
    }

    // Insert read status
    $stmt = $conn->prepare("INSERT IGNORE INTO announcement_reads (announcement_id, username) VALUES (?, ?)");
    $stmt->bind_param("is", $announcement_id, $_SESSION["username"]);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Failed to mark announcement as read');
    }
    
} catch (Exception $e) {
    error_log("Mark Announcement Read Error: " . $e->getMessage());
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    // Close the database connection
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?> 
?> 