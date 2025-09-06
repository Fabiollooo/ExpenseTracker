<?php
include 'Database/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $category = $_POST['category'];
    $title = $_POST['description'];
    $amount = $_POST['amount'];

    $sql = "DELETE FROM expenses WHERE date=? AND category=? AND title=? AND amount=?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }


    $stmt->bind_param("sssd", $date, $category, $title, $amount);
    $stmt->execute();
    $stmt->close();
}

header("Location: ../index.php");
exit;
