<?php
session_start();
require("../register/dbconnect.php");

if (!isset($_SESSION['id'])) {
    header('Location: ../register/login.php');
    exit();
}

$userId = (int) $_SESSION['id'];

if(isset($_POST["add"])){
    $stmt = $db->prepare(
    "INSERT INTO income(user_id,amount, category, type)
     VALUES(?,?, ?, ?)"
    );

    $stmt->execute([
    $userId,
    (int) $_POST['amountInput'],
    $_POST['category'],
    $_POST["type"]
    ]);

    header('Location: main.php');
    exit();
}
// SELECT
$stmt = $db->prepare("
SELECT * FROM income
WHERE user_id = ?
ORDER BY id DESC
");

$stmt->execute([$userId]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-white shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <i class="bi bi-wallet2 text-primary"></i>
            貯金
        </a>

        <button class="navbar-toggler" type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav">
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

<!-- Main -->
<div class="container-fluid py-4">
    <div class="row g-4">

        <!-- LEFT -->
        <div class="col-lg-5">

            <!-- Balance Card -->
            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-semibold">Balance</span>

                        <span class="badge bg-success-subtle text-success rounded-pill">
                            Good
                        </span>
                    </div>

                    <h1 class="fw-bold">$1,234.56</h1>

                    <div class="row mt-4">

                        <div class="col">
                            <p class="text-muted mb-1">Income</p>
                            <h5 class="text-success fw-bold">
                                +$789.01
                            </h5>
                        </div>

                        <div class="col border-start">
                            <p class="text-muted mb-1">Expenses</p>
                            <h5 class="text-danger fw-bold">
                                -$456.78
                            </h5>
                        </div>

                    </div>

                </div>
            </div>

            <!-- Form -->
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-body">

                    <h4 class="mb-3">Add Income</h4>

                    <form action="" method="POST">

                        <div class="mb-3">
                            <label class="form-label">
                                Amount ($)
                            </label>

                            <input
                                type="number"
                                class="form-control"
                                name="amountInput"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type</label>

                            <select class="form-select" name="type" id="type">
                                <option value="income">Income 💰</option>
                                <option value="expense">Expense 💸</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                Category
                            </label>

                            <select class="form-select" name="category" id="category">
                            </select>
                        </div>
                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                            name="add">
                            add
                        </button>

                    </form>

                </div>
            </div>

        </div>

        <!-- RIGHT -->
        <div class="col-lg-7">

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5>Recent Transactions</h5>
                    <p class="text-muted mb-0"></p>
                <?php if (empty($data)): ?>
                    <div class="text-muted">No transactions yet.</div>
                <?php else: ?>
                    <?php foreach ($data as $row): ?>
                        
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <span><?= htmlspecialchars($row['category']) ?></span>
                                                        <span class="<?= $row['type'] == 'income'
                                ? 'text-success'
                                : 'text-danger' ?> fw-bold">

                                <?= $row['type'] == 'income' ? '+' : '-' ?>
                                $<?= number_format($row['amount'],0) ?>
                            </span>

                            <form action="delete.php" method="POST" class="mb-0">
                                <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5>Statistics</h5>
                    <p class="text-muted mb-0">
                    </p>
                </div>
            </div>

        </div>

    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</html>