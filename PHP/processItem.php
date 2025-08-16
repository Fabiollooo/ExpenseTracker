<?php

//var_dump($_POST);


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $date = htmlspecialchars( $_POST['date']);
    $category = htmlspecialchars( $_POST['category']);
    $amount = htmlspecialchars( $_POST['amount']);
    $note = htmlspecialchars( $_POST['note']);

    echo "Date: $date\n";
    echo "Category: $category\n";
    echo "Amount: $amount\n";
    echo "Note: $note\n";
}
