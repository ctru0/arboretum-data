<?php
include 'includes/db.php';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="tree_measurements_'.date('Y-m-d').'.csv"');

$output = fopen('php://output', 'w');


fputcsv($output, array(
    'Tree Common Name',
    'Scientific Name',
    'Plantsoon URL',
    'Height 1 (m)',
    'Height 2 (m)',
    'Height 3 (m)',
    'Average Height (m)',
    'Circumference (cm)',
    'Submitted By',
    'Date Submitted'
));

// Write data
$query = "SELECT t.COMMON_NAME, t.SCIENTIFIC_NAME, t.PURL,
                m.HEIGHT_1, m.HEIGHT_2, m.HEIGHT_3, m.AVG_HEIGHT, m.CIRCUMFERENCE, 
                m.NETID, m.DATE_SUBMITTED
        FROM ENTRIES m
        JOIN trees t ON m.TREE_ID = t.TREE_ID
        ORDER BY m.DATE_SUBMITTED DESC";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, array(
        $row['COMMON_NAME'],
        $row['SCIENTIFIC_NAME'],
        $row['PURL'],
        $row['HEIGHT_1'],
        $row['HEIGHT_2'],
        $row['HEIGHT_3'],
        number_format($row['AVG_HEIGHT'], 2),
        $row['CIRCUMFERENCE'],
        $row['NETID'],
        date('m/d/Y', strtotime($row['DATE_SUBMITTED']))
    ));
}

fclose($output);
exit();
?>
