<?php
session_start();
require '../register/dbconnect.php';

if (!isset($_SESSION['id'])) {
    header('Location: ../register/login.php');
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$userId = (int) $_SESSION['id'];
$formError = '';
$categoryOptions = [
    'income' => ['Salary', 'Bonus', 'Investment'],
    'expense' => ['Food', 'Transportation', 'Shopping', 'Entertainment'],
];

if (isset($_POST['add'])) {
    $token = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        $formError = 'Invalid request. Please try again.';
    } else {
        $amountInput = isset($_POST['amountInput']) ? (float) $_POST['amountInput'] : 0;
        $type = $_POST['type'] ?? 'expense';
        $type = array_key_exists($type, $categoryOptions) ? $type : 'expense';
        $category = trim($_POST['category'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if (!in_array($category, $categoryOptions[$type], true)) {
            $category = $categoryOptions[$type][0];
        }

        if ($amountInput <= 0) {
            $formError = 'Amount must be greater than 0.';
        } else {
            $insertStmt = $db->prepare(
                'INSERT INTO income (user_id, amount, category, type, content, created_at)
                 VALUES (?, ?, ?, ?, ?, NOW())'
            );

            $insertStmt->execute([
                $userId,
                $amountInput,
                $category,
                $type,
                $content,
            ]);

            header('Location: main.php');
            exit();
        }
    }
}

$allowedPeriods = ['day', 'week', 'month'];
$period = $_GET['period'] ?? 'month';
$period = in_array($period, $allowedPeriods, true) ? $period : 'month';

if ($period === 'day') {
    $transactionsStmt = $db->prepare(
        'SELECT *
         FROM income
         WHERE user_id = ?
           AND DATE(created_at) = CURDATE()
         ORDER BY created_at DESC'
    );
} elseif ($period === 'week') {
    $transactionsStmt = $db->prepare(
        'SELECT *
         FROM income
         WHERE user_id = ?
           AND YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)
         ORDER BY created_at DESC'
    );
} else {
    $transactionsStmt = $db->prepare(
        'SELECT *
         FROM income
         WHERE user_id = ?
           AND MONTH(created_at) = MONTH(CURDATE())
           AND YEAR(created_at) = YEAR(CURDATE())
         ORDER BY created_at DESC'
    );
}

$transactionsStmt->execute([$userId]);
$data = $transactionsStmt->fetchAll();

$summaryStmt = $db->prepare(
    'SELECT
        COALESCE(SUM(CASE WHEN type = "income" THEN amount ELSE 0 END), 0) AS total_income,
        COALESCE(SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END), 0) AS total_expense
     FROM income
     WHERE user_id = ?
       AND MONTH(created_at) = MONTH(CURDATE())
       AND YEAR(created_at) = YEAR(CURDATE())'
);
$summaryStmt->execute([$userId]);
$summary = $summaryStmt->fetch() ?: [
    'total_income' => 0,
    'total_expense' => 0,
];

$totalIncome = (float) ($summary['total_income'] ?? 0);
$totalExpense = (float) ($summary['total_expense'] ?? 0);
$balance = $totalIncome - $totalExpense;
$expensePercent = $totalIncome > 0 ? min(100, (int) round(($totalExpense / $totalIncome) * 100)) : 0;

if ($totalIncome <= 0) {
    $balanceStatus = 'Needs Data';
    $balanceStatusClass = 'bg-secondary-subtle text-secondary';
} else {
    $balanceRatio = $balance / $totalIncome;

    if ($balanceRatio <= 1 / 3) {
        $balanceStatus = 'Bad';
        $balanceStatusClass = 'bg-danger-subtle text-danger';
    } elseif ($balanceRatio <= 2 / 3) {
        $balanceStatus = 'Warning';
        $balanceStatusClass = 'bg-warning-subtle text-warning';
    } else {
        $balanceStatus = 'Good';
        $balanceStatusClass = 'bg-success-subtle text-success';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/category.js" defer></script>
    <title>Personal Finance Tracker</title>
</head>
<body style="min-height:100vh;">
    <nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center gap-2" href="main.php">
                <i class="bi bi-wallet2 text-primary"></i>
                Personal Finance Tracker
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="main.php">
                            <i class="bi bi-house-door-fill"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">
                            <i class="bi bi-person-circle"></i>
                            <?= htmlspecialchars($_SESSION['name'] ?? 'User') ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../register/logout.php">
                            <i class="bi bi-box-arrow-right"></i> Log out
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="stat-icon bg-primary-subtle text-primary">
                                    <i class="bi bi-wallet2"></i>
                                </div>
                                <span class="fw-semibold">Balance</span>
                            </div>
                            <span class="badge <?= $balanceStatusClass ?> rounded-pill"><?= htmlspecialchars($balanceStatus) ?></span>
                        </div>

                        <h1 class="fw-bold mb-0">$<?= number_format($balance, 0) ?></h1>

                        <div class="progress mt-3" role="progressbar" aria-label="Expense percentage" aria-valuenow="<?= $expensePercent ?>" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar bg-danger" style="width: <?= $expensePercent ?>%"></div>
                        </div>
                        <small class="text-muted"><?= $expensePercent ?>% of this month's income has been spent.</small>

                        <div class="row mt-4">
                            <div class="col">
                                <div class="d-flex align-items-center gap-2 mb-1 text-muted">
                                    <i class="bi bi-arrow-up-circle-fill text-success"></i>
                                    <p class="mb-0">Income</p>
                                </div>
                                <h5 class="text-success fw-bold mb-0">+$<?= number_format($totalIncome, 0) ?></h5>
                            </div>

                            <div class="col border-start">
                                <div class="d-flex align-items-center gap-2 mb-1 text-muted">
                                    <i class="bi bi-arrow-down-circle-fill text-danger"></i>
                                    <p class="mb-0">Expenses</p>
                                </div>
                                <h5 class="text-danger fw-bold mb-0">-$<?= number_format($totalExpense, 0) ?></h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mt-4">
                    <div class="card-body">
                        <h4 class="mb-3">Add Transaction</h4>

                        <?php if ($formError !== ''): ?>
                            <div class="alert alert-danger p-2 small"><?= htmlspecialchars($formError) ?></div>
                        <?php endif; ?>

                        <form action="" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                            <div class="mb-3">
                                <label class="form-label">Amount ($)</label>
                                <input type="number" class="form-control" name="amountInput" value="" min="1" step="1" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Type</label>
                                <select class="form-select" name="type" id="type">
                                    <option value="income">Income</option>
                                    <option value="expense">Expense</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select class="form-select" name="category" id="category"></select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Content</label>
                                <input type="text" class="form-control" name="content" maxlength="255">
                            </div>

                            <button type="submit" class="btn btn-primary w-100" name="add">Add</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <h5 class="mb-0">Recent Transactions</h5>

                            <form action="" method="GET" class="ms-auto">
                                <label for="filter" class="visually-hidden">Filter period</label>
                                <select name="period" id="filter" class="form-select rounded w-auto" onchange="this.form.submit()">
                                    <option value="day"<?= $period === 'day' ? ' selected' : '' ?>>Day</option>
                                    <option value="week"<?= $period === 'week' ? ' selected' : '' ?>>Week</option>
                                    <option value="month"<?= $period === 'month' ? ' selected' : '' ?>>Month</option>
                                </select>
                            </form>
                        </div>

                        <?php if (empty($data)): ?>
                            <div class="text-muted mt-3">No transactions yet.</div>
                        <?php else: ?>
                            <?php foreach ($data as $row): ?>
                                <?php
                                $rowType = $row['type'] === 'income' ? 'income' : 'expense';
                                $category = trim((string) ($row['category'] ?? 'Other'));
                                $category = $category !== '' ? $category : 'Other';
                                $content = trim((string) ($row['content'] ?? ''));
                                $categoryInitial = strtoupper(substr($category, 0, 1));
                                $amountClass = $rowType === 'income' ? 'text-success' : 'text-danger';
                                $amountSign = $rowType === 'income' ? '+' : '-';
                                ?>
                                <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-warning-subtle rounded-4 p-3 me-3 transaction-icon">
                                            <?= htmlspecialchars($categoryInitial) ?>
                                        </div>

                                        <div>
                                            <div class="fw-bold">
                                                <?= htmlspecialchars($content !== '' ? $content : $category) ?>
                                            </div>
                                            <small class="text-muted">
                                                <?= htmlspecialchars($category) ?> - <?= htmlspecialchars($row['created_at'] ?? '') ?>
                                            </small>
                                        </div>
                                    </div>

                                    <div class="text-end">
                                        <div class="<?= $amountClass ?> fw-bold">
                                            <?= $amountSign ?>$<?= number_format((float) $row['amount'], 0) ?>
                                        </div>

                                        <div class="dropdown mt-1">
                                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-label="Transaction actions">
                                                <i class="bi bi-three-dots"></i>
                                            </button>

                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="edit.php?id=<?= (int) $row['id'] ?>">Edit</a>
                                                </li>
                                                <li>
                                                    <form action="delete.php" method="POST" onsubmit="return confirm('Delete this transaction?')">
                                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                                        <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                                        <button type="submit" class="dropdown-item text-danger">Delete</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
