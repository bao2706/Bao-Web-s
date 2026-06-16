<?php
session_start();
require('./dbconnect.php');

$error = [];

if (!empty($_POST)) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($name === '') {
        $error['name'] = 'blank';
    }

    if ($email === '') {
        $error['email'] = 'blank';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error['email'] = 'invalid';
    }

    if ($password === '') {
        $error['password'] = 'blank';
    } elseif (strlen($password) < 6) {
        $error['password'] = 'length';
    }

    if (!isset($error['email'])) {
        $stmt = $db->prepare('SELECT COUNT(*) FROM members WHERE email = ?');
        $stmt->execute([$email]);

        if ((int) $stmt->fetchColumn() > 0) {
            $error['email'] = 'duplicate';
        }
    }

    if (empty($error)) {
        $_SESSION['join'] = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ];

        header('Location: check.php');
        exit();
    }
}

if (isset($_REQUEST['action']) && $_REQUEST['action'] === 'rewrite' && isset($_SESSION['join'])) {
    $_POST = $_SESSION['join'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User | Personal Finance Tracker</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="../js/login.js" defer></script>
</head>
<body style="min-height: 100vh;">
    <div class="d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="col-11 col-sm-8 col-md-5 col-lg-4 p-4 bg-white rounded shadow-sm" style="margin: 50px auto;">
            <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
                <h2 class="text-center mb-4 text-dark fw-bold">Create User</h2>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show p-2 small mb-3 text-center" role="alert">
                        <?php
                        echo htmlspecialchars($_SESSION['error']);
                        unset($_SESSION['error']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="font-size: 0.5rem;top: 50%; transform: translateY(-50%);"></button>
                    </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label for="name" class="form-label">Full Name:</label>
                    <input type="text" name="name" id="name" class="form-control <?= isset($error['name']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                    <?php if (isset($error['name'])): ?>
                        <span class="invalid-feedback">Please enter your full name.</span>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" name="email" id="email" class="form-control <?= isset($error['email']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    <?php if (isset($error['email'])): ?>
                        <span class="invalid-feedback">
                            <?php
                            switch ($error['email']) {
                                case 'blank':
                                    echo 'Please enter your email.';
                                    break;
                                case 'invalid':
                                    echo 'Please enter a valid email address.';
                                    break;
                                case 'duplicate':
                                    echo 'This email is already registered.';
                                    break;
                            }
                            ?>
                        </span>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password:</label>
                    <input type="password" name="password" id="password" class="form-control <?= isset($error['password']) ? 'is-invalid' : '' ?>" required>
                    <?php if (isset($error['password'])): ?>
                        <span class="invalid-feedback">
                            <?php if ($error['password'] === 'blank'): ?>
                                Please enter your password.
                            <?php elseif ($error['password'] === 'length'): ?>
                                Password must be at least 6 characters long.
                            <?php endif; ?>
                        </span>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-success w-100">Create User</button>
                <p class="text-center my-2">or</p>
                <a href="login.php" class="btn btn-primary w-100">Back to Login</a>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
