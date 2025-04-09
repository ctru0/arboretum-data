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
        <a href="treedata.php">Tree Measurements</a>
        <a href="fieldnotes.php">Field Notes</a>
        <a href="view_tree_data.php">View All Data</a>
    </div>

    <!-- Content -->
    <div class="form-container">
        <h1>Tree Field Notes</h1>
        
        <form id="fieldNotes" action="submit_fieldnotes.php" method="POST">
            <!-- Tree Selection -->
            <label for="tree">Select Tree:</label>
            <select id="tree" name="TREE_ID" required>
            <option value="">-- Select a Tree --</option>
                <?php
                $trees = $conn->query("SELECT TREE_ID, COMMON_NAME FROM TREES ORDER BY COMMON_NAME");
                while ($tree = $trees->fetch_assoc()) {
                    echo "<option value='{$tree['TREE_ID']}'>{$tree['COMMON_NAME']}</option>";
                }
                ?>
            </select>
            
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
                
                <label class="checkbox-label">
                    <input type="checkbox" name="sign_missing" value="1">
                    Is the sign missing?
                </label>
            </div>

            <!-- Consolidated Notes Text Area -->
            <label for="notes">Field Notes:</label>
            <textarea id="notes" name="notes" rows="6" placeholder="Enter all observations about the tree and signage..."></textarea>

            <button type="submit" class="submit-btn">Submit Field Notes</button>
        </form>
    </div>

    <script>
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
    </script>
</body>
</html>
