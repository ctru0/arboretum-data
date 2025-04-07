<?php include 'includes/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Collect Field Notes</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <!-- Header -->
    <div class="header">
        <img src="assets/masonlogo.png" alt="Mason Logo">
        <h1><a href="index.php">Arboretum</a></h1>
        <a href="treedata.php">Tree Data</a>
        <a href="fieldnotes.php">Field Notes</a>
        <a href="view_data.php">View All Data</a>
    </div>

    <!-- Content -->
    <div class="form-container">
        <h1>Tree Field Notes</h1>
        
        <form id="fieldNotes" action="submit_fieldnotes.php" method="POST">
            <!-- Tree Selection -->
            <label for="TREE_ID">Select Tree:</label>
            <select id="TREE_ID" name="TREE_ID" required>
                <option value="">-- Select a Tree --</option>
                <?php
                $trees = $conn->query("SELECT TREE_ID, COMMON_NAME, SCIENTIFIC_NAME, URL FROM TREES ORDER BY COMMON_NAME");
                if ($trees && $trees->num_rows > 0) {
                    while ($tree = $trees->fetch_assoc()) {
                        echo "<option value='{$tree['TREE_ID']}'>
                                {$tree['COMMON_NAME']} ({$tree['SCIENTIFIC_NAME']})
                                </option>";
                    }
                } else {
                    echo "<option value=''>No trees available</option>";
                }
                ?>
            </select>
            
            <!-- Auto-filled Tree Info -->
            <div id="treeInfo" class="metadata-box">
                <p><strong>Scientific Name:</strong> <span id="scientificName">-</span></p>
                <p><strong>PlantSoon URL:</strong> <a id="PURL" href="#" target="_blank">-</a></p>
            </div>

            <!-- Checkbox Questions -->
            <div class="checkbox-group">                
                <label class="checkbox-label">
                    <input type="checkbox" name="tree_missing" value="1">
                    Is the tree missing?
                </label>
                
                <label class="checkbox-label">
                    <input type="checkbox" name="sign_missing" value="1">
                    Is the sign missing?
                </label>
            </div>

            <label for="notes">Field Notes:</label>
            <textarea id="notes" name="notes" rows="6" placeholder="Enter all observations about the tree and signage..."></textarea>

            <button type="submit" class="submit-btn">Submit Field Notes</button>
        </form>
    </div>

    <script>
        // Auto-fill tree info when selected
        document.getElementById('TREE_ID').addEventListener('change', function() {
            const treeId = this.value;
            if (!treeId) {
                document.getElementById('scientificName').textContent = '-';
                document.getElementById('PURL').textContent = '-';
                document.getElementById('PURL').href = '#';
                return;
            }

            fetch(`get_tree_metadata.php?tree_id=${treeId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('scientificName').textContent = data.SCIENTIFIC_NAME;
                    document.getElementById('PURL').textContent = data.URL;
                    document.getElementById('PURL').href = data.URL;
                })
                .catch(error => {
                    console.error('Error fetching tree data:', error);
                    document.getElementById('scientificName').textContent = 'Error loading data';
                    document.getElementById('PURL').textContent = 'Error loading data';
                    document.getElementById('PURL').href = '#';
                });
        });
    </script>
</body>
</html>
