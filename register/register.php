<?php
session_start();
require('./dbconnect.php');

$error = [];

if (!empty($_POST)) {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // 1. Validate name
    if ($name === '') {
        $error['name'] = 'blank';
    }

    // 2. Validate email
    if ($email === '') {
        $error['email'] = 'blank';
    }

    // 3. Validate password
    if ($password === '') {
        $error['password'] = 'blank';
    } elseif (strlen($password) < 6) {
        $error['password'] = 'length';
    }

    // 4. Check duplicate email (chỉ khi email hợp lệ)
    if (!isset($error['email'])) {

        $stmt = $db->prepare("SELECT COUNT(*) FROM members WHERE email = ?");
        $stmt->execute([$email]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $error['email'] = 'duplicate';
        }
    }

    // 5. Nếu không có lỗi → lưu session và chuyển trang
    if (empty($error)) {
        $_SESSION['join'] = $_POST;

        header('Location: check.php');
        exit();
    }
}

// 6. rewrite dữ liệu khi quay lại form
if (isset($_REQUEST['action']) && $_REQUEST['action'] === 'rewrite') {
    if (isset($_SESSION['join'])) {
        $_POST = $_SESSION['join'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src ="../js/login.js" defer></script>


</head>
<body style="background: linear-gradient(135deg, violet, skyBlue, lightGreen); min-height: 100vh;">
<div class="d-flex justify-content-center align-items-center" style="height: 100vh;">
<div class="col-11 col-sm-8 col-md-5 col-lg-4 p-4 bg-white rounded shadow-sm" style="margin: 50px auto;">
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
        <h2 class="text-center mb-4 text-dark fw-bold">Create User</h2>
    <div>
        <label for="name">Full Name:</label>
        <input type="text" name="name" id="name" class="form-control <?php echo isset($error['name']) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
        <?php if (isset($error['name'])): ?>
            <span class="invalid-feedback">Please enter your full name.</span>
        <?php endif; ?>
    </div>
    <div>
        <label for="email" >Email:</label>
        <input type="email" name="email" id="email" class="form-control <?php echo isset($error['email']) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        <?php if (isset($error['email'])): ?>
            <span class="invalid-feedback">
            <?php
            switch ($error['email']) {
                case 'blank':
                    echo 'Please enter your email.';
                    break;
                case 'duplicate':
                    echo 'This email is already registered.';
                    break;
            }
            ?>
            </span>
        <?php endif; ?>
    </div>
    <div>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" class="form-control <?php echo isset($error['password']) ? 'is-invalid' : ''; ?>">
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
    <div class="mt-3">
        <button type="submit" class="btn btn-success w-100">Create User</button>
    </div>
    <p class="text-center my-2">or</p>
    <a href="login.php" class="btn btn-primary w-100">Back to Login</a>
</form>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>