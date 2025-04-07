<?php include 'includes/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View All Data</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="header">
        <img src="assets/masonlogo.png" alt="Mason Logo">
        <h1><a href="index.php">Arboretum</a></h1>
        <a href="treedata.php">Tree Data</a>
    </div>
    <div class="data-view-container">
        <h1>Tree Measurements</h1>

        <div class="filter-section">
            <div class="filter-group">
                <label for="tree-filter">Filter by Tree:</label>
                <select id="tree-filter">
                    <option value="">All Trees</option>
                    <?php
                    $trees = $conn->query("SELECT tree_id, common_name FROM trees ORDER BY common_name");
                    while ($tree = $trees->fetch_assoc()) {
                        echo "<option value='{$tree['tree_id']}'>{$tree['common_name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="filter-group">
                <label for="person-filter">Filter by Person:</label>
                <input type="text" id="person-filter" placeholder="Enter name...">
            </div>

            <div class="filter-group">
                <label for="date-filter">Filter by Date:</label>
                <input type="date" id="date-filter">
            </div>

            <button id="reset-filters" class="filter-btn">Reset Filters</button>
        </div>

        <!-- Tree Data Table -->
        <div class="data-tab">
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
                <tbody id="tree-data-body">
                    <?php
                    $query = "SELECT t.common_name, t.scientific_name, 
                                    m.height1, m.height2, m.height3, m.circumference, 
                                    m.student_name, m.date_submitted
                                FROM measurements m
                                JOIN trees t ON m.tree_id = t.tree_id
                                ORDER BY m.date_submitted DESC";
                    $result = $conn->query($query);

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
                        <td><?= date('m/d/Y', strtotime($row['date_submitted'])) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Filtering -->
    <script>
    $(document).ready(function() {
        function applyFilters() {
            const treeFilter = $('#tree-filter').val();
            const personFilter = $('#person-filter').val().toLowerCase();
            const dateFilter = $('#date-filter').val();

            let formattedDate = '';
            if (dateFilter) {
                const parts = dateFilter.split('-'); 
                formattedDate = `${parts[1]}/${parts[2]}/${parts[0]}`; 
            }

            $('#tree-data-body tr').each(function() {
                const $row = $(this);
                const treeName = $row.find('td:eq(0)').text();
                const person = $row.find('td:eq(6)').text().toLowerCase();
                const date = $row.find('td:eq(7)').text();

                const showRow = 
                    (treeFilter === '' || treeName === $('#tree-filter option:selected').text()) &&
                    (personFilter === '' || person.includes(personFilter)) &&
                    (formattedDate === '' || date === formattedDate);

                $row.toggle(showRow);
            });
        }

        $('#tree-filter, #person-filter, #date-filter').on('change keyup', applyFilters);

        $('#reset-filters').click(function() {
            $('#tree-filter').val('');
            $('#person-filter').val('');
            $('#date-filter').val('');
            applyFilters();
        });
    });
    </script>
</body>
</html>
