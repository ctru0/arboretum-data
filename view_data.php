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
        <a href="fieldnotes.php">Field Notes</a>
        <a href="view_data.php">View All Data</a>
    </div>
    <div class="data-view-container">
        <h1>Tree Measurements</h1>

        <div class="filter-section">
            <div class="filter-group">
                <label for="tree-filter">Filter by Tree:</label>
                <select id="tree-filter">
                    <option value="">All Trees</option>
                    <?php
                    $trees = $conn->query("SELECT TREE_ID, COMMON_NAME FROM TREES ORDER BY COMMON_NAME");
                    while ($tree = $trees->fetch_assoc()) {
                        echo "<option value='{$tree['TREE_ID']}'>{$tree['COMMON_NAME']}</option>";
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
                    $query = "SELECT t.COMMON_NAME, t.SCIENTIFIC_NAME, 
                                    e.HEIGHT_1, e.HEIGHT_2, e.HEIGHT_3, e.CIRCUMFERENCE, 
                                    e.NETID, e.DATE_SUBMITTED
                                FROM ENTRIES e
                                JOIN TREES t ON e.TREE_ID = t.TREE_ID
                                ORDER BY e.DATE_SUBMITTED DESC";
                    $result = $conn->query($query);

                    while ($row = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($row['COMMON_NAME']) ?></td>
                        <td><?= htmlspecialchars($row['SCIENTIFIC_NAME']) ?></td>
                        <td><?= $row['HEIGHT_1'] ?></td>
                        <td><?= $row['HEIGHT_2'] ?></td>
                        <td><?= $row['HEIGHT_3'] ?></td>
                        <td><?= $row['CIRCUMFERENCE'] ?></td>
                        <td><?= htmlspecialchars($row['STUDENT_NAME']) ?></td>
                        <td><?= date('m/d/Y', strtotime($row['DATE_SUBMITTED'])) ?></td>
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
