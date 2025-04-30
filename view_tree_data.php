<?php include 'includes/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View All Data</title>

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
                    $years = $conn->query("SELECT DISTINCT YEAR(DATE_SUBMITTED) AS year FROM ENTRIES ORDER BY year DESC");
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
            <a href="export_tree_data.php?type=ENTRIES" class="download-buton">Download Tree Data (.csv)</a>
            <button id="delete-selected-btn" class="delete-selected-btn" disabled>Delete Selected</button>
        </div>

        <div class="data-tab">
            <table>
                <thead>
                    <tr>
                        <th class="checkbox-cell"><input type="checkbox" id="select-all" class="select-all-checkbox"></th>
                        <th>TREE</th>
                        <th>SCIENTIFIC NAME</th>
                        <th>HEIGHT 1 (m)</th>
                        <th>HEIGHT 2 (m)</th>
                        <th>HEIGHT 3 (m)</th>
                        <th>AVG HEIGHT (m)</th>
                        <th>CIRCUMFERENCE (cm)</th>
                        <th>SUBMITTED BY</th>
                        <th>DATE</th>
                    </tr>
                </thead>
                <tbody id="tree-data-body">
                    <?php
                    $query = "SELECT e.ENTRY_ID,  t.COMMON_NAME, t.SCIENTIFIC_NAME, 
                                    e.HEIGHT_1, e.HEIGHT_2, e.HEIGHT_3, e.AVG_HEIGHT, e.CIRCUMFERENCE, 
                                    e.NETID, e.DATE_SUBMITTED
                                FROM ENTRIES e
                                JOIN TREES t ON e.TREE_ID = t.TREE_ID
                                ORDER BY e.DATE_SUBMITTED DESC";
                    $result = $conn->query($query);

                    while ($row = $result->fetch_assoc()):
                    ?>
                    <tr data-entry-id="<?= $row['ENTRY_ID'] ?>">
                        <td class="checkbox-cell"><input type="checkbox" class="entry-checkbox" value="<?= $row['ENTRY_ID'] ?>"></td>
                        <td><?= htmlspecialchars($row['COMMON_NAME']) ?></td>
                        <td><?= htmlspecialchars($row['SCIENTIFIC_NAME']) ?></td>
                        <td><?= $row['HEIGHT_1'] ?></td>
                        <td><?= $row['HEIGHT_2'] ?></td>
                        <td><?= $row['HEIGHT_3'] ?></td>
                        <td><?= number_format($row['AVG_HEIGHT'], 2) ?></td>
                        <td><?= $row['CIRCUMFERENCE'] ?></td>
                        <td><?= htmlspecialchars($row['NETID']) ?></td>
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
        // Select/deselect all checkboxes
        $('#select-all').change(function() {
            $('.entry-checkbox').prop('checked', $(this).prop('checked'));
            updateDeleteButtonState();
        });

        // Update "select all" checkbox when individual checkboxes change
        $(document).on('change', '.entry-checkbox', function() {
            var allChecked = $('.entry-checkbox:checked').length === $('.entry-checkbox').length;
            $('#select-all').prop('checked', allChecked);
            updateDeleteButtonState();
        });

        // Enable/disable delete button based on selection
        function updateDeleteButtonState() {
            var anyChecked = $('.entry-checkbox:checked').length > 0;
            $('#delete-selected-btn').prop('disabled', !anyChecked);
        }

        // Delete selected entries
        $('#delete-selected-btn').click(function() {
            var selectedIds = [];
            $('.entry-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) return;

            if (!confirm('Are you sure you want to delete the selected ' + selectedIds.length + ' entries? This action cannot be undone.')) {
                return;
            }

            $.ajax({
                url: 'delete_entries.php',
                method: 'POST',
                data: { entry_ids: selectedIds },
                success: function(response) {
                    if (response.success) {
                        // Remove deleted rows
                        $('.entry-checkbox:checked').each(function() {
                            $(this).closest('tr').fadeOut(300, function() {
                                $(this).remove();
                            });
                        });
                        // Reset select all checkbox
                        $('#select-all').prop('checked', false);
                        // Disable delete button
                        $('#delete-selected-btn').prop('disabled', true);
                    } else {
                        alert('Error deleting entries: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error communicating with server');
                }
            });
        });

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
    <!-- collapsable nav -->
    <script src="assets/scripts.js"></script>
</body>
</html>
