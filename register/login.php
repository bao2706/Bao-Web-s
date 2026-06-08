<?php 
session_start();
require('./dbconnect.php');

$error = [];

if (!empty($_POST)) {

    $stmt = $db->prepare('SELECT * FROM members WHERE email=?');
    $stmt->execute([$_POST['email']]);
    $user = $stmt->fetch();

    if ($user && password_verify($_POST["password"], $user["password"])) {

        $_SESSION['id'] = $user['id'];
        $_SESSION['name'] = $user['name'];

        header('Location: ../main/main.php');
        exit();

    } else {
        $error['login'] = 'Invalid email or password';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body style=" min-height: 100vh;">

 
    <div class="d-flex justify-content-center align-items-center" style="height: 100vh;">

        <div class="col-11 col-sm-8 col-md-5 col-lg-4 p-4 bg-white rounded shadow-sm" style="margin: 50px auto;">
            <h2 class="text-center mb-4 text-dark fw-bold">Login</h2>
                    <?php  if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
        <?php
            echo $_SESSION['success'];
            unset($_SESSION['success']);
        ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
                    <?php if (isset($error['login'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show p-2 small mb-3 text-center" role="alert">
                            <?php echo $error['login'];?> 
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="font-size: 0.5rem;top: 50%; transform: translateY(-50%);"></button>
                    </div>  
                    <?php endif; ?>
 
        <form action="" method="post" >
                    <!-- Ô Password -->
            <div class="mb-3">
                <label for="passwordInput" class="form-label">Email:</label>
                <input type="email" class="form-control" id="emailInput" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="passwordInput" class="form-label">Password:</label>
                <input type="password" class="form-control" id="passwordInput" name="password">
            </div>
            <!-- Nút Login phủ kín chiều ngang của form cho đẹp -->
            <button type="submit" class="btn btn-success w-100">Login</button>
            <p class="text-center my-2">or</p>
            <a href="register.php" class="btn btn-primary w-100">Sign Up</a>
        </form>

    </div>
</div>
    </form>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</html>