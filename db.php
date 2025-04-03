<?php
$host = 'localhost';
$user = 'root';
$password = 'Password';
$database = 'Arboretum';


$conn = new mysqli($host, $user, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->query("CREATE DATABASE IF NOT EXISTS $database");
$conn->select_db($database);


$conn->query("
    CREATE TABLE IF NOT EXISTS trees (
        tree_id INT AUTO_INCREMENT PRIMARY KEY,
        common_name VARCHAR(50) NOT NULL,
        scientific_name VARCHAR(50) NOT NULL,
        PURL VARCHAR(20) NOT NULL,
        UNIQUE KEY (common_name)
    )");

$conn->query("
    CREATE TABLE IF NOT EXISTS measurements (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tree_id INT NOT NULL,
        height1 DECIMAL(5,2) NOT NULL,
        height2 DECIMAL(5,2) NOT NULL,
        height3 DECIMAL(5,2) NOT NULL,
        circumference DECIMAL(5,2) NOT NULL,
        student_name VARCHAR(50) NOT NULL,
        date_submitted TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (tree_id) REFERENCES trees(tree_id)
    )");

$conn->query("
    INSERT IGNORE INTO trees (common_name, scientific_name, PURL) VALUES
    ('TreeOfHeaven1', 'Ailanthus altissima', 'URL001'),
    ('Oak2', 'Quercus robur', 'URL002'),
    ('Maple3', 'Acer pseudoplatanus', 'URL003')
");
?>