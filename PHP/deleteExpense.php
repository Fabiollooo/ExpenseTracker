<?php

include 'Database/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $amount = $_POST['amount'];

    $sql = "DELETE FROM expenses WHERE Date=? AND Category=? AND Description=? AND Amount=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $date, $category, $description, $amount);
    $stmt->execute();
}

header("Location: ../index.php");
exit;