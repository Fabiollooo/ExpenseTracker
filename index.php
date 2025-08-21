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
      <div class="subtitle">August 2025</div>
      <a href="index.php?reset=1" style="margin-left:20px;color:red;">Reset Session</a>
      
      <form action="PHP/Database/reset.php" method="POST">
        <button class="btn secondary" type="submit">Reset Session</button>
      </form>
    </header>

    <!-- Make these dynamic, user enters the monthly budget, and it updates the values accordingly -->

    <section class="cards">
      <div class="card">
        <h3>Total Spent</h3>
        <div class="value">
          <?php foreach ($_SESSION['expenses'] as $expense): ?>
            <?php echo htmlspecialchars($expense['amount']) !== null ? '€' . number_format($expense['amount'], 2) : '€0.00'; ?>
          <?php endforeach; ?>
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
            if ($_SESSION['budget'] !== null) {
              $totalSpent = 0;
              foreach ($_SESSION['expenses'] as $expense) {
                $totalSpent += $expense['amount'];
              }
              echo '€' . number_format($_SESSION['budget'] - $totalSpent, 2);
            } else {
              echo '€0.00';
            }
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
        <div class="grow"><input type="search" placeholder="Search notes or category" /></div>
        <div><input type="month" /></div>
        <div>
          <select>
            <option value="">All categories</option>
            <option>Food</option>
            <option>Transport</option>
            <option>Groceries</option>
            <option>Shopping</option>
            <option>Bills</option>
            <option>Other</option>
          </select>
        </div>
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
          <tbody>
            <?php foreach ($_SESSION['expenses'] as $expense): ?>
              <tr>
                <td><?php echo htmlspecialchars($expense['date']); ?></td>
                <td><?php echo htmlspecialchars($expense['category']); ?></td>
                <td><?php echo htmlspecialchars($expense['note']); ?></td>
                <td class="amount">€<?php echo number_format($expense['amount'], 2); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <footer>Frontend mockup. Functionality will be added next.</footer>
  </div>
</body>
</html>


<!-- 
NOTES: 
- Implement some idea on a user possibly spending more than their budget
- Implement a live date tracker or somthing
- 
            -->