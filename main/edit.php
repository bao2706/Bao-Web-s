<?php
session_start();
require '../register/dbconnect.php';

if (!isset($_SESSION['id'])) {
    header('Location: ../register/login.php');
    exit();
}

$userId = (int) $_SESSION['id'];
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: main.php');
    exit();
}

$stmt = $db->prepare(
    'SELECT *
     FROM income
     WHERE id = ? AND user_id = ?
     LIMIT 1'
);
$stmt->execute([$id, $userId]);
$income = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$income) {
    header('Location: main.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $updateStmt = $db->prepare(
        'UPDATE income
         SET amount = ?, category = ?, type = ?, content = ?
         WHERE id = ? AND user_id = ?'
    );

    $updateStmt->execute([
        (int) $_POST['amountInput'],
        $_POST['category'],
        $_POST['type'],
        $_POST['content'],
        $id,
        $userId,
    ]);

    header('Location: main.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/category.js" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <title>Document</title>
</head>
<body class="bg-secondary bg-opacity-25 min-vh-100 d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h4 class="mb-3">Edit Income</h4>

                        <form action="" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Amount ($)</label>
                                <input type="number"
                                       class="form-control"
                                       name="amountInput"
                                       value="<?= htmlspecialchars((string) ($income['amount'] ?? 0)) ?>"
                                       required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Type</label>
                                <select class="form-select"
                                        name="type"
                                        id="type"
                                        data-current-type="<?= htmlspecialchars($income['type']) ?>">
                                    <option value="income">Income 💰</option>
                                    <option value="expense">Expense 💸</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select class="form-select"
                                        name="category"
                                        id="category"
                                        data-current-category="<?= htmlspecialchars($income['category'] ?? '') ?>"></select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Content</label>
                                <input type="text"
                                       class="form-control"
                                       name="content"
                                       value="<?= htmlspecialchars($income['content'] ?? '') ?>">
                            </div>

                            <button type="submit" class="btn btn-primary w-100" name="update">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
