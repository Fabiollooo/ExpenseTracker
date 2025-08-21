<?php
include 'db.php';


$conn->query("DELETE FROM expenses");

header("Location: ../../index.php");

exit();
