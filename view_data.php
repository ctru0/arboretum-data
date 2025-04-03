<?php include 'includes/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Tree Data</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="header">
        <img src="assets/masonlogo.png" alt="Logo">
        <h1><a href="index.php">Tree Measurements</a></h1>
        <a href="treedata.php">Add New Data</a>
    </div>

    <div class="table-container">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert success">Data submitted successfully!</div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Tree</th>
                    <th>Scientific Name</th>
                    <th>Height 1 (m)</th>
                    <th>Height 2 (m)</th>
                    <th>Height 3 (m)</th>
                    <th>Circumference (cm)</th>
                    <th>Submitted By</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("
                    SELECT t.common_name, t.scientific_name, 
                m.height1, m.height2, m.height3, 
                    m.circumference, m.student_name, 
                        DATE_FORMAT(m.date_submitted, '%Y-%m-%d %H:%i') as formatted_date
                    FROM measurements m
                    JOIN trees t ON m.tree_id = t.tree_id
                    ORDER BY m.date_submitted DESC
                ");

                while ($row = $result->fetch_assoc()):
                ?>
                    <tr>
                        <td><?= htmlspecialchars($row['common_name']) ?></td>
                        <td><?= htmlspecialchars($row['scientific_name']) ?></td>
                        <td><?= $row['height1'] ?></td>
                        <td><?= $row['height2'] ?></td>
                        <td><?= $row['height3'] ?></td>
                        <td><?= $row['circumference'] ?></td>
                        <td><?= htmlspecialchars($row['student_name']) ?></td>
                        <td><?= $row['formatted_date'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>