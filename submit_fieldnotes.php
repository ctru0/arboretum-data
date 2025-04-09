<?php
include 'includes/db.php';

// Get form data
$tree_id = (int)$_POST['TREE_ID'];
$tree_missing = isset($_POST['tree_missing']) ? 1 : 0;  // lowercase to match form
$sign_missing = isset($_POST['sign_missing']) ? 1 : 0;  // lowercase to match form
$tree_notes = isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : '';  // matches form field
$sign_notes = isset($_POST['sign_notes']) ? htmlspecialchars($_POST['sign_notes']) : '';  // matches form field
$netid = htmlspecialchars($_POST['NETID']);  // assuming you have this field


// Insert data - matches your table structure
$stmt = $conn->prepare("
    INSERT INTO FIELD_NOTES 
    (TREE_ID, NETID, TREE_MISSING, SIGN_MISSING, TREE_NOTES, OTHER_NOTES)
    VALUES (?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("isiiss", $tree_id, $netid, $tree_missing, $sign_missing, $tree_notes, $sign_notes);

if ($stmt->execute()) {
    header("Location: view_fieldnotes.php?success=1");
} else {
    header("Location: fieldnotes.php?error=1");
}

$stmt->close();
$conn->close();
?>