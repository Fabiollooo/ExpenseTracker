<?php
// setup_db.php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "expense_trackerdb";

$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>Database Setup Status</h2>";

// Check if database exists
$dbExists = $conn->select_db($dbname);

if ($dbExists) {
    echo "<p>Database <strong>$dbname</strong> already exists.</p>";
} else {
    // Create database
    if ($conn->query("CREATE DATABASE $dbname")) {
        echo "<p>Database <strong>$dbname</strong> created successfully.</p>";
        $dbExists = true;
    } else {
        die("<p>Error creating database: " . $conn->error . "</p>");
    }
}

if ($dbExists) {
    // Select the database
    $conn->select_db($dbname);

    // Check if table 'expenses' exists
    $result = $conn->query("SHOW TABLES LIKE 'expenses'");
    if ($result && $result->num_rows > 0) {
        echo "<p>Table <strong>expenses</strong> already exists.</p>";
    } else {
        // Create the expenses table
        $createTableSql = "CREATE TABLE expenses (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            amount DECIMAL(10, 2) NOT NULL,
            category VARCHAR(50) NOT NULL,
            date DATE NOT NULL
        )";

        if ($conn->query($createTableSql)) {
            echo "<p>Table <strong>expenses</strong> created successfully.</p>";
        } else {
            die("<p>Error creating table: " . $conn->error . "</p>");
        }
    }
}

$conn->close();

echo "<p><a href='../../index.php'>Return to Expense Tracker</a></p>";
