<?php
session_start();
require("../register/dbconnect.php");
if(isset($_POST["addIncome"])){
    $id =$_SESSION["id"];
    $amount =$_POST["amountInput"];
    $category =$_POST["category"];
    $stmt = $db->prepare(
    "INSERT INTO income(user_id,amount, content)
     VALUES(?,?, ?)"
    );

    $stmt->execute([
    $_SESSION["id"],
    $_POST['amountInput'],
    $_POST['category']
    ]);
}
// SELECT
$stmt = $db->prepare("
SELECT * FROM income WHERE user_id = ?
");

$stmt->execute([$_SESSION["id"]]);
$data = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">

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
                        <i class="bi bi-house-door-fill"></i>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-bell-fill"></i>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-person-circle"></i>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="../register/logout.php">
                        <i class="bi bi-box-arrow-right"></i>
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
                            <label class="form-label">
                                Category
                            </label>

                            <select class="form-select" name="category">
                                <option>Food & Dining 🍔</option>
                                <option>Transportation 🚗</option>
                                <option>Shopping 🛍️</option>
                                <option>Entertainment 🎬</option>
                            </select>
                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                            name="addIncome">
                            Add Income
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
                <?php foreach ($data as $row): ?>
                    <div>
                        <?= $row['amount'] ?> - <?= $row['content'] ?>
                    </div>
                <?php endforeach; ?>
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