<?php
include 'includes/db.php';


$TREE_ID = (int)$_POST['TREE_ID'];
$HEIGHT_1 = (float)$_POST['HEIGHT_1'];
$HEIGHT_2 = (float)$_POST['HEIGHT_2'];
$HEIGHT_3 = (float)$_POST['HEIGHT_3'];
$CIRCUMFERENCE = (float)$_POST['CIRCUMFERENCE'];
$NETID = htmlspecialchars(trim($_POST['NETID']));

$stmt = $conn->prepare("
    INSERT INTO ENTRIES 
    (TREE_ID, HEIGHT_1, HEIGHT_2, HEIGHT_3, CIRCUMFERENCE, NETID) 
    VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("idddds", $TREE_ID, $HEIGHT_1, $HEIGHT_2, $HEIGHT_3, $CIRCUMFERENCE, $NETID);

if ($stmt->execute()) {
    header("Location: view_data.php?success=1");
} else {
    header("Location: treedata.php?error=1");
}
?>
