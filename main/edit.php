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
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$formError = '';
$categoryOptions = [
    'income' => ['Salary', 'Bonus', 'Investment'],
    'expense' => ['Food', 'Transportation', 'Shopping', 'Entertainment'],
];

if ($id <= 0) {
    header('Location: main.php');
    exit();
}

$stmt = $db->prepare(
    'SELECT id, amount, category, type, content
     FROM income
     WHERE id = ? AND user_id = ?
     LIMIT 1'
);
$stmt->execute([$id, $userId]);
$income = $stmt->fetch();

if (!$income) {
    header('Location: main.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
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
            $updateStmt = $db->prepare(
                'UPDATE income
                 SET amount = ?, category = ?, type = ?, content = ?
                 WHERE id = ? AND user_id = ?'
            );

            $updateStmt->execute([
                $amountInput,
                $category,
                $type,
                $content,
                $id,
                $userId,
            ]);

            header('Location: main.php');
            exit();
        }
    }
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
    <title>Edit Transaction | Personal Finance Tracker</title>
</head>
<body class="bg-secondary bg-opacity-25 min-vh-100 d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">Edit Transaction</h4>
                            <a href="main.php" class="btn btn-sm btn-light">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>
                        </div>

                        <?php if ($formError !== ''): ?>
                            <div class="alert alert-danger p-2 small"><?= htmlspecialchars($formError) ?></div>
                        <?php endif; ?>

                        <form action="" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                            <div class="mb-3">
                                <label class="form-label">Amount ($)</label>
                                <input type="number"
                                       class="form-control"
                                       name="amountInput"
                                       value="<?= htmlspecialchars((string) ($income['amount'] ?? 0)) ?>"
                                       min="1"
                                       step="1"
                                       required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Type</label>
                                <select class="form-select"
                                        name="type"
                                        id="type"
                                        data-current-type="<?= htmlspecialchars($income['type']) ?>">
                                    <option value="income">Income</option>
                                    <option value="expense">Expense</option>
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
                                       maxlength="255"
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
