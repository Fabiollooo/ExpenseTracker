<?php
session_start();
include 'PHP/Database/db.php'; 

    // Handle reset
    if (isset($_GET['reset'])) {
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit;
    }

    // Initialize session variables
    if (!isset($_SESSION['budget'])) {
        $_SESSION['budget'] = null;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_budget'])) {
        $_SESSION['budget'] = (float) $_POST['budget'];
        header("Location: index.php");
        exit;
    }

    if (!isset($_SESSION['expenses'])) {
        $_SESSION['expenses'] = [];
    }

    // Filters
    $category = $_GET['categoryFilter'] ?? '';
    $month = $_GET['month'] ?? '';
    $search = $_GET['search'] ?? '';


    $sql = "SELECT Date AS date, Category AS category, title AS description, Amount AS amount FROM expenses WHERE 1=1";

    // Add filters dynamically
    if ($category) {
        $sql .= " AND Category = '" . $conn->real_escape_string($category) . "'";
    }

    if ($month) {
        $month = $conn->real_escape_string($month);
        $sql .= " AND DATE_FORMAT(Date, '%Y-%m') = '$month'";
    }

    if ($search) {
        $search = $conn->real_escape_string($search);
        $sql .= " AND (title LIKE '%$search%' OR Category LIKE '%$search%')";
    }

    $sql .= " ORDER BY Date DESC";

    // Execute query
    $result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Tracker</title>
    <link rel="stylesheet" href="style/style.css">
</head>

<body>

<?php if ($_SESSION['budget'] === null): ?>
<div class="overlay">
  <div class="popup">
    <h2>Set Monthly Budget</h2>
    <form method="post">
      <input type="number" step="0.01" name="budget" placeholder="Enter your budget" required>
      <button type="submit" name="set_budget">Save</button>
    </form>
  </div>
</div>
<?php endif; ?>

<div class="container">
  <header>
    <div>
      <div class="title">Expense Tracker</div>
      <div class="subtitle">Track spending, categories, and monthly totals</div>
    </div>

    <div class="subtitle">
      <p id="date"></p>
      <script>
        function updateDate() {
          const today = new Date();
          const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
          document.getElementById('date').textContent = today.toLocaleDateString(undefined, options);
        }
        updateDate(); 
        setInterval(updateDate, 10000); 
      </script>
    </div>
    
    <a href="index.php?reset=1" style="margin-left:20px;color:red;">Reset Session</a>
    
      <form action="PHP/Database/reset.php" method="POST">
        <button class="btn secondary" type="submit">Reset DB</button>
      </form>
      
      <form method="GET" action="PHP/Database/setup_db.php" target="_blank">
        <button type="submit">Setup Database</button>
      </form>

  </header>

  <section class="cards">
    <div class="card">
      <h3>Total Spent</h3>
      <div class="value">
        <?php
        $sqlTotal = "SELECT SUM(Amount) AS total FROM expenses WHERE Date >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        $resTotal = $conn->query($sqlTotal);
        $rowTotal = $resTotal->fetch_assoc();
        $total = $rowTotal['total'] !== null ? (float) $rowTotal['total'] : 0;
        echo '€' . number_format($total, 2);
        ?>
      </div>
    </div>
    <div class="card">
      <h3>Monthly Budget</h3>
      <div class="value">
        <?php echo $_SESSION['budget'] !== null ? '€' . number_format($_SESSION['budget'], 2) : '€0.00'; ?>
      </div>
    </div>
    <div class="card">
      <h3>Remaining</h3>
      <div class="value">
        <?php
        $remaining = $_SESSION['budget'] - $total;
        echo '€' . number_format($remaining, 2);
        ?>
      </div>
    </div>
  </section>

  <section class="panel" aria-labelledby="add-expense-title">
    <div class="panel-header">
      <div id="add-expense-title" class="panel-title">Add Expense</div>
    </div>

    <form action="PHP/processItem.php" method="POST">
      <div class="col-2">
        <label for="date">Date</label>
        <input required id="date" type="date" name="date" />
      </div>
      <div class="col-2">
        <label for="category">Category</label>
        <select required id="category" name="category">
          <option>Food</option>
          <option>Transport</option>
          <option>Groceries</option>
          <option>Shopping</option>
          <option>Bills</option>
          <option>Other</option>
        </select>
      </div>
      <div>
        <label for="amount">Amount</label>
        <input required id="amount" name="amount" type="number" step="0.01" placeholder="0.00" />
      </div>
      <div class="col-3">
        <label for="description">Description</label>
        <input required id="description" name="description" type="text" placeholder="e.g. lunch, bus fare" />
      </div>
      <div class="actions">
        <button class="btn" type="submit" name="add_expense">Add</button>
        <button class="btn secondary" type="button">Clear</button>
      </div>
    </form>
  </section>

  <section class="panel" style="margin-top: 18px" aria-labelledby="history-title">
    <div class="panel-header">
      <div id="history-title" class="panel-title">Expenses</div>
    </div>

    <div class="toolbar">
      <form method="GET" class="filters-form">
        <div>
          <input type="search" name="search" placeholder="Search notes or category" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" />
        </div>
        <div>
          <input type="month" name="month" value="<?php echo htmlspecialchars($_GET['month'] ?? ''); ?>" />
        </div>
        <div>
          <select name="categoryFilter" onchange="this.form.submit()">
            <option value="" <?php if ($category == '') echo 'selected'; ?>>All categories</option>
            <option value="Food" <?php if ($category == 'Food') echo 'selected'; ?>>Food</option>
            <option value="Transport" <?php if ($category == 'Transport') echo 'selected'; ?>>Transport</option>
            <option value="Groceries" <?php if ($category == 'Groceries') echo 'selected'; ?>>Groceries</option>
            <option value="Shopping" <?php if ($category == 'Shopping') echo 'selected'; ?>>Shopping</option>
            <option value="Bills" <?php if ($category == 'Bills') echo 'selected'; ?>>Bills</option>
            <option value="Other" <?php if ($category == 'Other') echo 'selected'; ?>>Other</option>
          </select>
        </div>
        <div>
          <button type="submit" class="btn">Apply</button>
        </div>
      </form>
    </div>

    <div style="overflow:auto">
      <table>
        <thead>
          <tr>
            <th>Date</th>
            <th>Category</th>
            <th>Note</th>
            <th class="amount">Amount</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php 
          if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
        ?>
          <tr>
            <td><?php echo htmlspecialchars($row['date']); ?></td>
            <td><?php echo htmlspecialchars($row['category']); ?></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td class="amount"><?php echo '€' . number_format($row['amount'], 2); ?></td>
            <td>
              <div class="expense-actions">
                <form action="PHP/editExpense.php" method="GET">
                  <input type="hidden" name="date" value="<?php echo htmlspecialchars($row['date']); ?>">
                  <input type="hidden" name="category" value="<?php echo htmlspecialchars($row['category']); ?>">
                  <input type="hidden" name="description" value="<?php echo htmlspecialchars($row['description']); ?>">
                  <input type="hidden" name="amount" value="<?php echo htmlspecialchars($row['amount']); ?>">
                  <button class="btn secondary" type="submit">Edit</button>
                </form>
                <form action="PHP/deleteExpense.php" method="POST" onsubmit="return confirm('Delete this expense?');">
                  <input type="hidden" name="date" value="<?php echo htmlspecialchars($row['date']); ?>">
                  <input type="hidden" name="category" value="<?php echo htmlspecialchars($row['category']); ?>">
                  <input type="hidden" name="description" value="<?php echo htmlspecialchars($row['description']); ?>">
                  <input type="hidden" name="amount" value="<?php echo htmlspecialchars($row['amount']); ?>">
                  <button class="btn secondary" type="submit">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        <?php 
            }
          } else {
            echo '<tr><td colspan="5">No expenses found.</td></tr>';
          }
        ?>
        </tbody>
      </table>

        <div class="total-expenses">
          <p>
            Total Expenses: 
           <?php
            $sqlTotal = "SELECT SUM(Amount) AS total FROM expenses WHERE Date >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
            $resTotal = $conn->query($sqlTotal);
            $rowTotal = $resTotal->fetch_assoc();
            $total = $rowTotal['total'] !== null ? (float) $rowTotal['total'] : 0;
            echo '€' . number_format($total, 2);
          ?>
          </p>
        </div>
        
      </div>
  </section>
</div>
</body>
</html>
