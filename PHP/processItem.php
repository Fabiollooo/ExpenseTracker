<?php
include 'Database/db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_expense'])) {

    $category = htmlspecialchars($_POST['category']);
    $amount = $_POST['amount'];
    $description = htmlspecialchars($_POST['description']);
    $date = $_POST['date'];

    $stmt = $conn->prepare("INSERT INTO expenses (Category, Amount, Description, `Date`) VALUES (?, ?, ?, ?)");
    if (!$stmt){
        die("Prepare failed: " . $conn->error); 
    }

    $stmt->bind_param("sdss", $category, $amount, $description, $date);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    header("Location: ../index.php");

    exit();
}
