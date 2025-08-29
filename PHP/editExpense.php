<?php

include 'Database/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update logic here
    $old_date = $_POST['old_date'];
    $old_category = $_POST['old_category'];
    $old_description = $_POST['old_description'];
    $old_amount = $_POST['old_amount'];

    $date = $_POST['date'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $amount = $_POST['amount'];

    $sql = "UPDATE expenses SET Date=?, Category=?, Description=?, Amount=? WHERE Date=? AND Category=? AND Description=? AND Amount=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $date, $category, $description, $amount, $old_date, $old_category, $old_description, $old_amount);
    $stmt->execute();
    header("Location: ../index.php");
    exit;
} else {
    // Show edit form
    $date = $_GET['date'];
    $category = $_GET['category'];
    $description = $_GET['description'];
    $amount = $_GET['amount'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Expense</title>
    <link rel="stylesheet" href="../style/style.css">
</head>
<body>
<div class="container">
    <h2>Edit Expense</h2>
    <form method="POST">
        <input type="hidden" name="old_date" value="<?php echo htmlspecialchars($date); ?>">
        <input type="hidden" name="old_category" value="<?php echo htmlspecialchars($category); ?>">
        <input type="hidden" name="old_description" value="<?php echo htmlspecialchars($description); ?>">
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
        <input type="text" name="description" value="<?php echo htmlspecialchars($description); ?>" required>
        <button class="btn" type="submit">Update</button>
        <a class="btn secondary" href="../index.php">Cancel</a>
    </form>
</div>
</body>
</html>