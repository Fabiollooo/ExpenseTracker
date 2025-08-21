<?php

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the form data

    $date = htmlspecialchars( $_POST['date']);
    $category = htmlspecialchars( $_POST['category']);
    $amount = htmlspecialchars( $_POST['amount']);
    $note = htmlspecialchars( $_POST['note']);

    $sql = "INSERT INTO expenses (date, category, amount, note) VALUES ('$date', '$category', '$amount', '$note')";

    if ($conn->query($sql) === TRUE) {
        echo "New expense added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

}

$conn->close();
?>