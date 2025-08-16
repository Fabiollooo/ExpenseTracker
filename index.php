
<?php
session_start();

// make sure the expenses list exists
if (!isset($_SESSION['expenses'])) {
    $_SESSION['expenses'] = [];
}

// when form is submitted, add the new expense
if (isset($_POST['add'])) {
    $date = $_POST['date'];
    $category = $_POST['category'];
    $amount = $_POST['amount'];
    $note = $_POST['note'];

    $_SESSION['expenses'][] = [
        'date' => $date,
        'category' => $category,
        'amount' => $amount,
        'note' => $note
    ];
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
  <div class="container">
    <header>
      <div>
        <div class="title">Expense Tracker</div>
        <div class="subtitle">Track spending, categories, and monthly totals</div>
      </div>
      <div class="subtitle">August 2025</div>
    </header>

    <section class="cards">
      <div class="card">
        <h3>Total Spent</h3>
        <div class="value">€0.00</div>
      </div>
      <div class="card">
        <h3>Monthly Budget</h3>
        <div class="value">€1,000.00</div>
      </div>
      <div class="card">
        <h3>Remaining</h3>
        <div class="value">€1,000.00</div>
      </div>
    </section>

    <section class="panel" aria-labelledby="add-expense-title">
      <div class="panel-header">
        <div id="add-expense-title" class="panel-title">Add Expense</div>
      </div>



      <form action="PHP/processItem.php" method="POST">
        <div class="col-2">
          <label for="date">Date</label>
          <input id="date" type="date" name="date" />
        </div>
        <div class="col-2">
          <label for="category">Category</label>
          <select id="category" name="category">
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
          <input id="amount" name="amount" type="number" step="0.01" placeholder="0.00" />
        </div>
        <div class="col-3">
          <label for="note">Note</label>
          <input id="note" name="note" type="text" placeholder="e.g. lunch, bus fare" />
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
            
          </tbody>
        </table>
      </div>
    </section>

    <footer>Frontend mockup. Functionality will be added next.</footer>
  </div>
</body>



</html>
