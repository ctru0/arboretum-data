<?php include 'includes/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Collect Field Notes</title>

    <!-- import stylesheet and font -->
    <link rel="stylesheet" href="assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Figtree&display=swap" rel="stylesheet">
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

    <!-- Content -->
    <div class="form-container">
        <h1>Field Notes</h1>
        
        <form id="fieldNotes" action="submit_fieldnotes.php" method="POST">
            <!-- Tree Selection -->
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
            
            <!-- Select tree by URL (NOT IMPLEMENTED YET) -->
            <div class="filter-group">
                <label for="URL-filter">Select by Plantsoon URL:</label>
                <input type="text" id="URL-filter" placeholder="Enter Plantsoon URL...">
                <button type="button" id="clear-filter" class="clear-btn">Clear</button>
            </div>

            <!-- Auto-filled Tree Info -->
            <div id="treeMetadata" class="metadata-box">
                <p><strong>Scientific Name:</strong> <span id="sciName">-</span></p>
                <p><strong>Plantsoon URL:</strong> <span id="PURL">-</span></p>
            </div>

            <!-- Checkbox Questions -->
            <div class="checkbox-group">                
                <label class="checkbox-label">
                    <input type="checkbox" name="tree_missing" value="1">
                    Is the tree missing?
                </label>
            </div>

            <label for="notes">Field Notes:</label>
            <textarea id="notes" name="notes" rows="6" placeholder="Write your observations here. Include any notable characteristics, changes, or concerns.
"></textarea>
            
            <div class="checkbox-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="sign_missing" value="1">
                    Sign is missing or damaged
                </label>
            </div>

            <label for="notes">Sign Notes:</label>
            <textarea id="sign_notes" name="sign_notes" rows="6" placeholder="Write your observations here. Include any notable characteristics, changes, or concerns.
"></textarea>
            
            <div class="student-info">
                <label for="netid">Your NetID:</label>
                <input type="text" id="netid" name="NETID" required>
            </div>

            <button type="submit" class="submit-btn">Submit Field Notes</button>
        </form>
    </div>

    <script>
        const treeSelect = document.getElementById('tree'); 
        const urlInput = document.getElementById('URL-filter');
        // Auto-fill tree info when selected
        document.getElementById('tree').addEventListener('change', function() {
            const treeId = this.value;
            if (!treeId) {
                document.getElementById('sciName').textContent = '-';
                document.getElementById('PURL').textContent = '-';
                return;
            }

            fetch(`get_tree_metadata.php?tree_id=${treeId}`)
                .then(response => response.json())
                .then(data => {
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
        <!-- collapsable nav -->
    <script src="assets/scripts.js"></script>
</body>
</html>
