<?php
session_start();
//var_dump($_POST);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $date = htmlspecialchars( $_POST['date']);
    $category = htmlspecialchars( $_POST['category']);
    $amount = htmlspecialchars( $_POST['amount']);
    $note = htmlspecialchars( $_POST['note']);

    if ($date && $category && $amount) {
        $_SESSION['expenses'][] = [
            'date' => $date,
            'category' => $category,
            'amount' => $amount,
            'note' => $note
        ];
    }


    // redirects the user back to the main page
    header("Location: ../index.php");
    exit();

} else {
    //just in case if the user manages to access this page somehow.
    header("Location: ../index.php");
    exit();
}
