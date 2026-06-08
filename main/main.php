<?php
session_start(); 
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
<body style=" min-height: 100vh;">
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-wallet2 text-primary"></i>
                貯金
            </a>
            <button class ="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
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
                        <a class="nav-link" href="../register/register.php">
                            <i class="bi bi-bell-fill"></i>
                        </a>
                    </li> 
                    <li class="nav-item">
                        <a class="nav-link" href="../register/register.php">Register</a>
                    </li> 
                    <li class="nav-item">
                        <a class="nav-link" href="../register/logout.php">
                            <i class="bi bi-box-arrow-right"></i>
                        </a>
                    </li>
                </ul>
            </div>
    </nav>
<div class="row g-4">
    <div class="col-lg-5 ">
        <div class="d-flex flex-column gap-3">
            <div class=" p-3 bg-light text-dark rounded shadow-sm card-apple ">
                <div class="d-flex justify-content-between align-items-center mb-2 rounded">
                    <p >Balance:</p>
                    <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1">Good</span>
                </div>
                    <h1>$1,234.56</h1>
                    <div class="row">
                        <div class="col">
                            <p>Income</p>
                            <p class="text-success fw-bold">+$789.01</p>
                        </div>
                        <div class="col border-start">
                            <p>Expenses</p>
                            <p class="text-danger fw-bold">$456.78</p>
                        </div>
                    </div>
                </div>
            </div>


            <div class="p-3 bg-success text-white rounded">
                <p>Add Income</p>
            </div>
        </div>
    <div class="col lg-7">
        <div class="bg-info text-white p-3 rounded">
        </div>
        <div class="bg-danger text-dark p-3 rounded">

        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</html>