<?php include 'includes/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Field Notes</title>
    <!-- import stylesheet and font -->
    <link rel="stylesheet" href="assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Figtree&display=swap" rel="stylesheet">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <img src="assets/masonlogo.png" alt="Mason Logo">
        <h1><a href="index.php">GMU Arboretum</a></h1>
        
        <button id="menu-toggle" class="menu-toggle">☰</button>

        <div class="nav-links">
            <a href="treedata.php">Tree Measurements</a>
            <a href="fieldnotes.php">Field Notes</a>
            <a href="view_tree_data.php">View Tree Data</a>
            <a href="view_fieldnotes.php">View Field Notes</a>
        </div>
    </div>
    
    <div class="data-view-container">
        <h1>Field Notes</h1>

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
                <input type="text" id="person-filter" placeholder="Enter NetID...">
            </div>

            <div class="filter-group">
                <label for="date-filter">Filter by Date:</label>
                <input type="date" id="date-filter">
            </div>

            <div class="filter-group">
                <label for="year-filter">Filter by Year:</label>
                <select id="year-filter">
                <option value="">All Years</option>
                <?php
                    $years = $conn->query("SELECT DISTINCT YEAR(DATE_SUBMITTED) AS year FROM FIELD_NOTES ORDER BY year DESC");
                    while ($year = $years->fetch_assoc()) {
                        echo "<option value='{$year['year']}'>{$year['year']}</option>";
                    }
                ?>
                </select>
            </div>


            <button id="reset-filters" class="filter-btn">Reset Filters</button>
        </div>

    <!-- Download CSV Button -->
    <div class="action-buttons">
            <a href="export_fieldnotes.php?type=FIELD_NOTES" class="download-buton">Download Field Notes (.csv)</a>
        </div>
        
        <div class="data-tab">
            <table>
                <thead>
                    <tr>
                        <th>Tree</th>
                        <th>Scientific Name</th>
                        <th>Submitted By</th>
                        <th>Tree Missing</th>
                        <th>Sign Missing</th>
                        <th>Notes</th>
                        <th>Other Notes</th>
                        <th>Date Submitted</th>
                    </tr>
                </thead>
                <tbody id="fieldnotes-data-body">
                    <?php
                    $query = "SELECT 
                                    t.COMMON_NAME, 
                                    t.SCIENTIFIC_NAME, 
                                    fn.NETID, 
                                    fn.TREE_MISSING, 
                                    fn.SIGN_MISSING, 
                                    fn.TREE_NOTES, 
                                    fn.OTHER_NOTES, 
                                    fn.DATE_SUBMITTED
                                FROM FIELD_NOTES fn
                                JOIN TREES t ON fn.TREE_ID = t.TREE_ID
                                ORDER BY fn.DATE_SUBMITTED DESC";
                    $result = $conn->query($query);

                    while ($row = $result->fetch_assoc()):
                        $treeMissing = $row['TREE_MISSING'] ? 'Yes' : 'No';
                        $signMissing = $row['SIGN_MISSING'] ? 'Yes' : 'No';
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($row['COMMON_NAME']) ?></td>
                        <td><?= htmlspecialchars($row['SCIENTIFIC_NAME']) ?></td>
                        <td><?= htmlspecialchars($row['NETID']) ?></td>
                        <td><?= $treeMissing ?></td>
                        <td><?= $signMissing ?></td>
                        <td><?= htmlspecialchars($row['TREE_NOTES']) ?></td>
                        <td><?= htmlspecialchars($row['OTHER_NOTES']) ?></td>
                        <td><?= date('m/d/Y', strtotime($row['DATE_SUBMITTED'])) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

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

            $('#fieldnotes-data-body tr').each(function() {
                const $row = $(this);
                const treeName = $row.find('td:eq(0)').text();
                const person = $row.find('td:eq(2)').text().toLowerCase();
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
    <!-- collapsable nav -->
    <script src="assets/scripts.js"></script>
</body>
</html>