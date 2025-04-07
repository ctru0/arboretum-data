<?php
include 'includes/db.php';


$tree_id = (int)$_POST['tree_id'];
$height1 = (float)$_POST['height1'];
$height2 = (float)$_POST['height2'];
$height3 = (float)$_POST['height3'];
$circumference = (float)$_POST['circumference'];
$student_name = htmlspecialchars(trim($_POST['student_name']));

$stmt = $conn->prepare("
    INSERT INTO measurements 
    (tree_id, height1, height2, height3, circumference, student_name) 
    VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("idddds", $tree_id, $height1, $height2, $height3, $circumference, $student_name);

if ($stmt->execute()) {
    header("Location: view_data.php?success=1");
} else {
    header("Location: treedata.php?error=1");
}
?>