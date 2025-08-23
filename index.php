<?php
session_start();
include 'PHP/Database/db.php';

// Reset only if requested
if (isset($_GET['reset'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
}

// Initialize budget if not set
if (!isset($_SESSION['budget'])) {
    $_SESSION['budget'] = null;
}

// Set budget if submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_budget'])) {
    $_SESSION['budget'] = (float) $_POST['budget'];
    header("Location: index.php");
    exit;
}

// Initialize expenses if not set
if (!isset($_SESSION['expenses'])) {
    $_SESSION['expenses'] = [];
}


//Filters

$category = $_GET['categoryFilter'] ?? '';
$sql = "SELECT Date AS date, Category AS category, Description AS description, Amount AS amount FROM expenses";
if ($category) {
    $sql .= " WHERE Category = '" . $conn->real_escape_string($category) . "'";
}
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
    </header>

    

    <section class="cards">
      <div class="card">
        <h3>Total Spent</h3>
        <div class="value">
          <?php
            $sql = "SELECT SUM(Amount) AS total FROM expenses WHERE Date >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $total = $row['total'] !== null ? (float) $row['total'] : 0;
            echo '€' . number_format($total, 2);
          ?>
        </div>
      </div>
      <div class="card">
        <h3>Monthly Budget</h3>
        <div class="value" name="monthly_budget">
          <?php echo $_SESSION['budget'] !== null ? '€' . number_format($_SESSION['budget'], 2) : '€0.00'; ?>
        </div>
        
      </div>
      <div class="card">
        <h3>Remaining</h3>
        <div class="value">
          <?php
            $sumSql = "SELECT SUM(Amount) AS total FROM expenses WHERE Date >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
            $sumResult = $conn->query($sumSql);
            $sumRow = $sumResult->fetch_assoc();
            $total = $sumRow['total'] !== null ? (float) $sumRow['total'] : 0;
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




    <!-- Expense History -->
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
            </tr>
          </thead>


          <!-- Displayes the items from the db -->
          <tbody>
          <?php 
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['date']) . "</td>
                            <td>" . htmlspecialchars($row['category']) . "</td>
                            <td>" . htmlspecialchars($row['description']) . "</td>
                            <td>" . htmlspecialchars($row['amount']) . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No expenses recorded.</td></tr>";
            }
            $conn->close();
            ?>

          </tbody>

        </table>
      </div>
    </section>


  </div>
</body>
</html>


<!-- 
NOTES: 
- Implement some idea on a user possibly spending more than their budget
- Implement a live date tracker or somthing
- 
            -->