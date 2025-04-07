<?php
header('Content-Type: application/json');
include 'includes/db.php';

$treeId = (int)$_GET['tree_id'];
$stmt = $conn->prepare("SELECT SCIENTIFIC_NAME, URL FROM TREES WHERE tTREE_ID = ?");
$stmt->bind_param("i", $treeId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode(['error' => 'Tree not found']);
}
?>
