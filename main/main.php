<?php
session_start();
require '../register/dbconnect.php';

if (!isset($_SESSION['id'])) {
    header('Location: ../register/login.php');
    exit();
}

$userId = (int) $_SESSION['id'];

if (isset($_POST['add'])) {
    $insertStmt = $db->prepare(
        'INSERT INTO income (user_id, amount, category, type, content, created_at)
         VALUES (?, ?, ?, ?, ?, NOW())'
    );

    $insertStmt->execute([
        $userId,
        (int) $_POST['amountInput'],
        $_POST['category'],
        $_POST['type'],
        $_POST['content'],
    ]);

    header('Location: main.php');
    exit();
}

$period = $_GET['period'] ?? 'month';

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
$data = $transactionsStmt->fetchAll(PDO::FETCH_ASSOC);

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
$summary = $summaryStmt->fetch(PDO::FETCH_ASSOC) ?: [
    'total_income' => 0,
    'total_expense' => 0,
];

$totalIncome = (int) ($summary['total_income'] ?? 0);
$totalExpense = (int) ($summary['total_expense'] ?? 0);
$balance = $totalIncome - $totalExpense;
$expensePercent = $totalIncome > 0 ? min(100, (int) round(($totalExpense / $totalIncome) * 100)) : 0;

if ($totalIncome <= 0) {
    $balanceStatus = 'Bad';
    $balanceStatusClass = 'bg-danger-subtle text-danger';
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
    <title>貯金ウェブサイト</title>
</head>
<body style="min-height:100vh;">
    <nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="bi bi-wallet2 text-primary"></i>
                貯金
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="main.php">
                            <p class="bi bi-house-door-fill"> Home</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <p class="bi bi-bell-fill"> Notification</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <p class="bi bi-person-circle"> Profile</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../register/logout.php">
                            <p class="bi bi-box-arrow-right"> Log out</p>
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

                        <div class="d-flex align-items-center gap-3">
                            <h1 class="fw-bold mb-0">$<?= number_format($balance) ?></h1>
                        </div>

                        <div class="row mt-4">
                            <div class="col">
                                <div class="d-flex align-items-center gap-2 mb-1 text-muted">
                                    <i class="bi bi-arrow-up-circle-fill text-success"></i>
                                    <p class="mb-0">Income</p>
                                </div>
                                <h5 class="text-success fw-bold mb-0">+$<?= number_format($totalIncome) ?></h5>
                            </div>

                            <div class="col border-start">
                                <div class="d-flex align-items-center gap-2 mb-1 text-muted">
                                    <i class="bi bi-arrow-down-circle-fill text-danger"></i>
                                    <p class="mb-0">Expenses</p>
                                </div>
                                <h5 class="text-danger fw-bold mb-0">-$<?= number_format($totalExpense) ?></h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mt-4">
                    <div class="card-body">
                        <h4 class="mb-3">Add Income</h4>

                        <form action="" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Amount ($)</label>
                                <input type="number" class="form-control" name="amountInput" value="0" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Type</label>
                                <select class="form-select" name="type" id="type">
                                    <option value="income">Income 💰</option>
                                    <option value="expense">Expense 💸</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select class="form-select" name="category" id="category"></select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Content</label>
                                <input type="text" class="form-control" name="content">
                            </div>

                            <button type="submit" class="btn btn-primary w-100" name="add">Add</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0">Recent Transactions</h5>

                            <form action="" method="GET" class="ms-auto">
                                <select name="period" id="filter" class="form-select rounded w-auto" onchange="this.form.submit()">
                                    <option value="day">Day</option>
                                    <option value="week">Week</option>
                                    <option value="month">Month</option>
                                </select>
                            </form>
                        </div>

                        <p class="text-muted mb-0"></p>

                        <?php if (empty($data)): ?>
                            <div class="text-muted">No transactions yet.</div>
                        <?php else: ?>
                            <?php foreach ($data as $row): ?>
                                <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-warning-subtle rounded-4 p-3 me-3">
                                            <?= htmlspecialchars(mb_substr($row['category'], -1)) ?>
                                        </div>

                                        <div>
                                            <div class="fw-bold">
                                                <?= htmlspecialchars($row['content'] ?? $row['category']) ?>
                                            </div>
                                            <small class="text-muted">
                                                <?= htmlspecialchars($row['created_at'] ?? '') ?>
                                            </small>
                                        </div>
                                    </div>

                                    <div class="text-end">
                                        <div class="<?= $row['type'] == 'income' ? 'text-success' : 'text-danger' ?> fw-bold">
                                            <?= $row['type'] == 'income' ? '+' : '-' ?>
                                            ¥<?= number_format($row['amount']) ?>
                                        </div>

                                        <div class="dropdown mt-1">
                                            <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots"></i>
                                            </button>

                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="edit.php?id=<?= $row['id'] ?>">✏️ Edit</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger"
                                                       href="delete.php?id=<?= $row['id'] ?>"
                                                       onclick="return confirm('Delete this content?')">🗑 Delete</a>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
