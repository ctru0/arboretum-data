<?php
include 'includes/db.php';


$tree_id = (int)$_POST['TREE_ID'];
$height1 = (float)$_POST['HEIGHT_1'];
$height2 = (float)$_POST['HEIGHT_2'];
$height3 = (float)$_POST['HEIGHT_3'];
$circumference = (float)$_POST['CIRCUMFERENCE'];
$netid = htmlspecialchars(trim($_POST['NETID']));

$stmt = $conn->prepare("
    INSERT INTO ENTRIES 
    (TREE_ID, HEIGHT_1, HEIGHT_2, HEIGHT_3, CIRCUMFERENCE, NETID) 
    VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("idddds", $tree_id, $height1, $height2, $height3, $circumference, $netid);

if ($stmt->execute()) {
    header("Location: view_tree_data.php?success=1");
} else {
    header("Location: treedata.php?error=1");
}
?>
