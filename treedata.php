<?php include 'includes/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tree Data Entry</title>

    <!-- import stylesheet and font -->
    <link rel="stylesheet" href="assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Figtree&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <div class="header">
        <img src="assets/masonlogo.png" alt="Mason Logo">
        <h1><a href="index.php">GMU Arboretum</a></h1>
        <a href="treedata.php">Tree Measurements</a>
        <a href="fieldnotes.php">Field Notes</a>
        <a href="view_tree_data.php">View Tree Data</a>
        <a href="view_fieldnotes.php">View Field Notes</a>
    </div>

    <div class="form-container">
        <form id="treeForm" action="submit_data.php" method="POST">
            <label for="tree">Select Tree:</label>
            <select id="tree" name="TREE_ID" required>
                <option value="">-- Select a Tree --</option>
                <?php
                $trees = $conn->query("SELECT TREE_ID, SCIENTIFIC_NAME, PURL FROM TREES ORDER BY SCIENTIFIC_NAME");
                while ($tree = $trees->fetch_assoc()) {
                    echo "<option value='{$tree['TREE_ID']}' data-purl='{$tree['PURL']}'>{$tree['SCIENTIFIC_NAME']}</option>";
                }
                ?>
            </select>

            <div class="filter-group">
                <label for="URL-filter">Select by Plantsoon URL:</label>
                <input type="text" id="URL-filter" placeholder="Enter Plantsoon URL...">
                <button type="button" id="clear-filter" class="clear-btn">Clear</button>
            </div>

            <div id="treeMetadata" class="metadata-box">
                <p><strong>Common Name:</strong> <span id="commonName">-</span></p>
                <p><strong>Scientific Name:</strong> <span id="sciName">-</span></p>
                <p><strong>Plantsoon URL:</strong> <span id="PURL">-</span></p>
            </div>

            <div class="measurements">
                <h3>Measurements</h3>
                <div class="measurement-group">
                    <label for="height1">Height 1 (m):</label>
                    <input type="number" id="height1" name="HEIGHT_1" step="0.01" min="0" required>
                </div>
                
                <div class="measurement-group">
                    <label for="height2">Height 2 (m):</label>
                    <input type="number" id="height2" name="HEIGHT_2" step="0.01" min="0" required>
                </div>
                
                <div class="measurement-group">
                    <label for="height3">Height 3 (m):</label>
                    <input type="number" id="height3" name="HEIGHT_3" step="0.01" min="0" required>
                </div>
                
                <div class="measurement-group">
                    <label for="circumference">Circumference (cm):</label>
                    <input type="number" id="circumference" name="CIRCUMFERENCE" step="0.1" min="0" required>
                </div>
            </div>

            <div class="student-info">
                <label for="netid">Your NetID:</label>
                <input type="text" id="netid" name="NETID" required>
            </div>

            <button type="submit" class="submit-btn">Submit Data</button>
        </form>
    </div>

    <script>
        const treeSelect = document.getElementById('tree');
        const urlInput = document.getElementById('URL-filter');

        treeSelect.addEventListener('change', function () {
            const treeId = this.value;
            if (!treeId) {
                document.getElementById('commonName').textContent = '-';
                document.getElementById('sciName').textContent = '-';
                document.getElementById('PURL').textContent = '-';
                return;
            }

            fetch(`get_tree_metadata.php?tree_id=${treeId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('commonName').textContent = data.COMMON_NAME;
                    document.getElementById('sciName').textContent = data.SCIENTIFIC_NAME;
                    document.getElementById('PURL').textContent = data.PURL;
                })
                .catch(error => console.error('Error:', error));
        });

        urlInput.addEventListener('input', function () {
            const filterValue = this.value.toLowerCase();
            const options = treeSelect.querySelectorAll('option');
            let matchFound = false;

            options.forEach(option => {
                if (option.value === '') {
                    option.style.display = '';
                    return;
                }

                const purl = option.getAttribute('data-purl') || '';
                const match = purl.toLowerCase().includes(filterValue);

                option.style.display = match ? '' : 'none';

                if (purl.toLowerCase() === filterValue) {
                    treeSelect.value = option.value;
                    treeSelect.dispatchEvent(new Event('change'));
                    matchFound = true;
                }
            });

            const noResultsOption = treeSelect.querySelector('option[data-no-results]');
            if (!matchFound && filterValue !== '') {
                if (!noResultsOption) {
                    const newOption = document.createElement('option');
                    newOption.value = '';
                    newOption.textContent = '-- No trees found --';
                    newOption.setAttribute('data-no-results', 'true');
                    treeSelect.appendChild(newOption);
                }
            } else {
                if (noResultsOption) {
                    noResultsOption.remove();
                }
            }
        });

        document.getElementById('clear-filter').addEventListener('click', function () {
            urlInput.value = '';
            const options = treeSelect.querySelectorAll('option');
            options.forEach(option => option.style.display = '');
            const noResultsOption = treeSelect.querySelector('option[data-no-results]');
            if (noResultsOption) noResultsOption.remove();
        });
    </script>

</body>
</html>
