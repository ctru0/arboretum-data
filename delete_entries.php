<?php
include 'includes/db.php';

header('Content-Type: application/json');

// Check if request is POST and has entry_ids
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['entry_ids'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

// Sanitize input - ensure we have an array of integers
$entryIds = array_map('intval', $_POST['entry_ids']);
$placeholders = implode(',', array_fill(0, count($entryIds), '?'));

try {
    // Prepare and execute delete statement
    $stmt = $conn->prepare("DELETE FROM ENTRIES WHERE ENTRY_ID IN ($placeholders)");
    
    // Bind parameters dynamically
    $types = str_repeat('i', count($entryIds));
    $stmt->bind_param($types, ...$entryIds);
    $stmt->execute();
    
    // Check if deletion was successful
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'deleted_count' => $stmt->affected_rows]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No entries were deleted']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>