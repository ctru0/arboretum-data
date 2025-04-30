<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = 'localhost';
$user = 'root';
$password = 'Password';
$database = 'ARBORETUM_DB';

$conn = new mysqli($host, $user, $password);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->query("CREATE DATABASE IF NOT EXISTS $database");
$conn->select_db($database);


$conn->query("
    CREATE TABLE IF NOT EXISTS TREES (
        TREE_ID INT AUTO_INCREMENT PRIMARY KEY,
        COMMON_NAME VARCHAR(50) NOT NULL,
        SCIENTIFIC_NAME VARCHAR(70) NOT NULL,
        PURL VARCHAR(266) NOT NULL UNIQUE
        );"
    );


$conn->query("
    CREATE TABLE IF NOT EXISTS ENTRIES (
        ENTRY_ID INT AUTO_INCREMENT PRIMARY KEY,
        TREE_ID INT NOT NULL,
        HEIGHT_1 DECIMAL(5,2) NOT NULL,
        HEIGHT_2 DECIMAL(5,2) NOT NULL,
        HEIGHT_3 DECIMAL(5,2) NOT NULL,
        AVG_HEIGHT DECIMAL(5,2) NOT NULL,
        CIRCUMFERENCE DECIMAL(5,2) NOT NULL,
        NETID VARCHAR(15) NOT NULL,
        DATE_SUBMITTED TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (TREE_ID) REFERENCES TREES(TREE_ID)
    );"
    );

$conn->query("
    CREATE TABLE IF NOT EXISTS FIELD_NOTES (
	NOTES_ID INT AUTO_INCREMENT PRIMARY KEY NOT NULL UNIQUE,
    TREE_ID INT NOT NULL,
    FOREIGN KEY (TREE_ID) REFERENCES TREES(TREE_ID),
    NETID VARCHAR(15) NOT NULL,
    TREE_MISSING INT(1) DEFAULT 0,
    SIGN_MISSING INT(1) DEFAULT 0,
    TREE_NOTES LONGTEXT,
    DATE_SUBMITTED TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    OTHER_NOTES LONGTEXT
    );"
);

$conn->query("
    INSERT IGNORE INTO TREES (COMMON_NAME, SCIENTIFIC_NAME, PURL) VALUES
        ('TreeOfHeaven1', 'Ailanthus altissima', 'URL001'),
        ('Oak2', 'Quercus robur', 'URL002'),
        ('Maple3', 'Acer pseudoplatanus', 'URL003');"
 );
?>
