<?php
include 'includes/db.php';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="tree_measurements_'.date('Y-m-d').'.csv"');

$output = fopen('php://output', 'w');

fputcsv($output, array(
    'Tree Common Name',
    'Scientific Name',
    'Plantsoon URL',
    'Tree Missing?',
    'Sign Missing?',
    'Tree Notes',
    'Sign Notes',
    'Submitted By',
    'Date Submitted'
));

$query = "SELECT t.COMMON_NAME, t.SCIENTIFIC_NAME, t.PURL,
                m.TREE_MISSING, m.SIGN_MISSING, m.TREE_NOTES, m.OTHER_NOTES, 
                m.NETID, m.DATE_SUBMITTED
        FROM FIELD_NOTES m
        JOIN trees t ON m.TREE_ID = t.TREE_ID
        ORDER BY m.DATE_SUBMITTED DESC";
$result = $conn->query($query);

if ($result === false) {
    die("Query failed: " . $conn->error);
}

while ($row = $result->fetch_assoc()) {
    fputcsv($output, array(
        $row['COMMON_NAME'],
        $row['SCIENTIFIC_NAME'],
        $row['PURL'],
        $row['TREE_MISSING'] ? 'Yes' : 'No',  
        $row['SIGN_MISSING'] ? 'Yes' : 'No',  
        $row['TREE_NOTES'],
        $row['OTHER_NOTES'],
        $row['NETID'],
        date('m/d/Y', strtotime($row['DATE_SUBMITTED']))
    ));
}

fclose($output);
$conn->close();
exit();
?>