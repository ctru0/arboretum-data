<?php
$host = 'localhost';
$user = 'root';
$password = 'Password';
$database = 'ARBORETUM_DB';


$conn = new mysqli($host, $user, $password);

if (!$conn->select_db($database)) {
    die("Database selection failed: " . $conn->error);
}

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->query("CREATE DATABASE IF NOT EXISTS $database");
$conn->select_db($database);

$conn->query("
    CREATE TABLE IF NOT EXISTS TREES (
    	TREE_ID INT AUTO_INCREMENT PRIMARY KEY NOT NULL UNIQUE,
    	URL VARCHAR(60) NOT NULL UNIQUE,
	COMMON_NAME VARCHAR(50) NOT NULL,
    	SCIENTIFIC_NAME VARCHAR(40) NOT NULL
    );"
);


$conn->query("
    CREATE TABLE IF NOT EXISTS ENTRIES (
	ENTRY_ID INT PRIMARY KEY AUTO_INCREMENT NOT NULL UNIQUE,
    DATE_SUBMITTED TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CIRCUMFERENCE DECIMAL(3,2) NOT NULL,
    DIAMETER DECIMAL(3,2),
    HEIGHT_1 DECIMAL(3,2) NOT NULL,
    HEIGHT_2 DECIMAL(3,2) NOT NULL,
    HEIGHT_3 DECIMAL(3,2) NOT NULL,
    AVG_HEIGHT DECIMAL(3,2) NOT NULL,
    TREE_ID INT NOT NULL,
    FOREIGN KEY (TREE_ID) REFERENCES TREES(TREE_ID),
    NETID VARCHAR(15) NOT NULL,
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
    INSERT IGNORE INTO TREES (COMMON_NAME, SCIENTIFIC_NAME, URL) VALUES
        ('TreeOfHeaven1', 'Ailanthus altissima', 'URL001'),
        ('Oak2', 'Quercus robur', 'URL002'),
        ('Maple3', 'Acer pseudoplatanus', 'URL003');"
 );
?>
