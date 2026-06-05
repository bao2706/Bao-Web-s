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
</head>
<body>
<form action="index.php" method="post" enctype="multipart/form-data">
    <h1>Create User</h1>
    <div>
        <label for="name">Full Name:</label>
        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
        <?php if (isset($error['name'])): ?>
            <span class="error">Please enter your full name.</span>
        <?php endif; ?>
    </div>
    <div>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        <?php if (isset($error['email'])): ?>
            <span class="error">
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
        <input type="password" name="password" id="password">
        <?php if (isset($error['password'])): ?>
            <span class="error">
                <?php if ($error['password'] === 'blank'): ?>
                    Please enter your password.
                <?php elseif ($error['password'] === 'length'): ?>
                    Password must be at least 6 characters long.
                <?php endif; ?>
            </span>
        <?php endif; ?>
    </div>
    <button type="submit">Create User</button>
</form>
</body>
</html>