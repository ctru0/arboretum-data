<?php include 'includes/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tree Data Entry</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="header">
        <img src="assets/masonlogo.png" alt="Logo">
        <h1><a href="index.php">Tree Data Entry</a></h1>
        <a href="treedata.php">Tree Data</a>
        <a href="fieldnotes.php">Field Notes</a>
        <a href="view_data.php">View All Data</a>
    </div>

    <div class="form-container">
        <form id="treeForm" action="submit_data.php" method="POST">
            <label for="tree">Select Tree:</label>
            <select id="tree" name="tree_id" required>
                <option value="">-- Select a Tree --</option>
                <?php
                $trees = $conn->query("SELECT tree_id, common_name FROM trees ORDER BY common_name");
                while ($tree = $trees->fetch_assoc()) {
                    echo "<option value='{$tree['tree_id']}'>{$tree['common_name']}</option>";
                }
                ?>
            </select>

            <div id="treeMetadata" class="metadata-box">
                <p><strong>Scientific Name:</strong> <span id="sciName">-</span></p>
                <p><strong>URL Code:</strong> <span id="urlCode">-</span></p>
            </div>

            <div class="measurements">
                <h3>Measurements</h3>
                <div class="measurement-group">
                    <label for="height1">Height 1 (m):</label>
                    <input type="number" id="height1" name="height1" step="0.01" min="0" required>
                </div>
                
                <div class="measurement-group">
                    <label for="height2">Height 2 (m):</label>
                    <input type="number" id="height2" name="height2" step="0.01" min="0" required>
                </div>
                
                <div class="measurement-group">
                    <label for="height3">Height 3 (m):</label>
                    <input type="number" id="height3" name="height3" step="0.01" min="0" required>
                </div>
                
                <div class="measurement-group">
                    <label for="circumference">Circumference (cm):</label>
                    <input type="number" id="circumference" name="circumference" step="0.1" min="0" required>
                </div>
            </div>

            <div class="student-info">
                <label for="NetID">Your Name:</label>
                <input type="text" id="student_name" name="student_name" required>
            </div>

            <button type="submit" class="submit-btn">Submit Data</button>
        </form>
    </div>

    <script>
        document.getElementById('tree').addEventListener('change', function() {
            const treeId = this.value;
            if (!treeId) {
                document.getElementById('sciName').textContent = '-';
                document.getElementById('urlCode').textContent = '-';
                return;
            }

            fetch(`get_tree_metadata.php?tree_id=${treeId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('sciName').textContent = data.scientific_name;
                    document.getElementById('urlCode').textContent = data.PURL;
                })
                .catch(error => console.error('Error:', error));
        });
    </script>
</body>
</html>