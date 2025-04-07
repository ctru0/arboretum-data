<?php
include 'includes/db.php';

// Get form data
$tree_id = (int)$_POST['tree_id'];
$tree_missing = isset($_POST['tree_missing']) ? 1 : 0;
$sign_missing = isset($_POST['sign_missing']) ? 1 : 0;
$notes = htmlspecialchars($_POST['notes']);
$date_submitted = date('Y-m-d H:i:s');



// Insert data
$stmt = $conn->prepare("
    INSERT INTO field_notes 
    (tree_id, tree_missing, sign_missing, notes)
    VALUES (?, ?, ?, ?)
");
$stmt->bind_param("iiis", $tree_id, $tree_missing, $sign_missing, $notes);

if ($stmt->execute()) {
    header("Location: fieldnotes.php?success=1");
} else {
    header("Location: fieldnotes.php?error=1");
}
?>