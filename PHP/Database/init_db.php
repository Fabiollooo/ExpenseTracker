<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "expense_trackerdb";

// Step 1: Connect to MySQL **without** specifying a database
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Step 2: Create the database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) !== TRUE) {
    die("Error creating database: " . $conn->error);
}

// Step 3: Select the database
$conn->select_db($dbname);

// Step 4: Create the expenses table if it doesn't exist
$tableSql = "
CREATE TABLE IF NOT EXISTS expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    category VARCHAR(100),
    date DATE
)";

if ($conn->query($tableSql) !== TRUE) {
    die("Error creating table: " . $conn->error);
}


$result = $conn->query("SELECT COUNT(*) as count FROM expenses");
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    $conn->query("
        INSERT INTO expenses (title, amount, category, date) VALUES
        ('Groceries', 50.75, 'Food', '2025-09-01'),
        ('Bus Ticket', 2.50, 'Transport', '2025-09-02'),
        ('Movie Night', 12.00, 'Entertainment', '2025-09-03')
    ");
}

echo "✅ Database and table setup complete.";
$conn->close();
?>
