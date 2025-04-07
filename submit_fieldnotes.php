<?php
include 'includes/db.php';

// Get form data
$tree_id = (int)$_POST['TREE_ID'];
$tree_missing = isset($_POST['TREE_MISSING']) ? 1 : 0;
$sign_missing = isset($_POST['SIGN_MISSING']) ? 1 : 0;
$notes = htmlspecialchars($_POST['TREE_NOTES']);
$date_submitted = date('Y-m-d H:i:s');



// Insert data
$stmt = $conn->prepare("
    INSERT INTO FIELD_NOTES 
    (TREE_ID, TREE_MISSING, SIGN_MISSING, TREE_NOTES)
    VALUES (?, ?, ?, ?)
");
$stmt->bind_param("iiis", $tree_id, $tree_missing, $sign_missing, $notes);

if ($stmt->execute()) {
    header("Location: fieldnotes.php?success=1");
} else {
    header("Location: fieldnotes.php?error=1");
}
?>
