<?php
// Enable all errors and display them
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
 
    include __DIR__ . '/Database/db.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $old_date = $_POST['old_date'] ?? null;
        $old_category = $_POST['old_category'] ?? null;
        $old_title = $_POST['old_description'] ?? null;
        $old_amount = $_POST['old_amount'] ?? null;

      
        $date = $_POST['date'] ?? null;
        $category = $_POST['category'] ?? null;
        $title = $_POST['description'] ?? null;
        $amount = $_POST['amount'] ?? null;

     
        if (!$old_date || !$old_category || !$old_title || !$old_amount) {
            die("Missing old expense data for update.");
        }
        if (!$date || !$category || !$title || !$amount) {
            die("Missing new expense data.");
        }

      
        $sql = "UPDATE expenses SET date=?, category=?, title=?, amount=? WHERE date=? AND category=? AND title=? AND amount=?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

       
        $stmt->bind_param("sssdsssd", $date, $category, $title, $amount, $old_date, $old_category, $old_title, $old_amount);

        if (!$stmt->execute()) {
            die("Execute failed: " . $stmt->error);
        }

        $stmt->close();
        $conn->close();

        header("Location: ../index.php");
        exit;
    } else {
       
        $date = $_GET['date'] ?? null;
        $category = $_GET['category'] ?? null;
        $title = $_GET['description'] ?? null;
        $amount = $_GET['amount'] ?? null;

        if (!$date || !$category || !$title || !$amount) {
            die("Missing expense data in URL parameters.");
        }
    }
} catch (Exception $e) {
    echo "<pre>Exception caught: " . $e->getMessage() . "</pre>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Expense</title>
    <link rel="stylesheet" href="../style/style.css">
</head>
<body>
<div class="container">
    <h2>Edit Expense</h2>
    <form method="POST">
        <input type="hidden" name="old_date" value="<?php echo htmlspecialchars($date); ?>">
        <input type="hidden" name="old_category" value="<?php echo htmlspecialchars($category); ?>">
        <input type="hidden" name="old_description" value="<?php echo htmlspecialchars($title); ?>">
        <input type="hidden" name="old_amount" value="<?php echo htmlspecialchars($amount); ?>">

        <label>Date</label>
        <input type="date" name="date" value="<?php echo htmlspecialchars($date); ?>" required>

        <label>Category</label>
        <select name="category" required>
            <option <?php if ($category == 'Food') echo 'selected'; ?>>Food</option>
            <option <?php if ($category == 'Transport') echo 'selected'; ?>>Transport</option>
            <option <?php if ($category == 'Groceries') echo 'selected'; ?>>Groceries</option>
            <option <?php if ($category == 'Shopping') echo 'selected'; ?>>Shopping</option>
            <option <?php if ($category == 'Bills') echo 'selected'; ?>>Bills</option>
            <option <?php if ($category == 'Other') echo 'selected'; ?>>Other</option>
        </select>

        <label>Amount</label>
        <input type="number" step="0.01" name="amount" value="<?php echo htmlspecialchars($amount); ?>" required>

        <label>Description</label>
        <input type="text" name="description" value="<?php echo htmlspecialchars($title); ?>" required>

        <button class="btn" type="submit">Update</button>
        <a class="btn secondary" href="../index.php">Cancel</a>
    </form>
</div>
</body>
</html>
