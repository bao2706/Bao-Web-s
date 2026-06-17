<?php
session_start();
require __DIR__ . '/dbconnect.php';

if (!isset($_SESSION['join'])) {
    header('Location: register.php');
    exit();
}

if (
    empty($_SESSION['join']['name']) ||
    empty($_SESSION['join']['email']) ||
    empty($_SESSION['join']['password'])
) {
    header('Location: register.php');
    exit();
}

$name = trim($_SESSION['join']['name']);
$email = trim($_SESSION['join']['email']);
$password = password_hash(trim($_SESSION['join']['password']), PASSWORD_DEFAULT);

try {
    $stmt = $db->prepare('INSERT INTO members (name, email, password) VALUES (?, ?, ?)');
    $stmt->execute([$name, $email, $password]);
} catch (PDOException $e) {
    $_SESSION['error'] = 'Could not create your account. Please try again.';

    if (isset($e->errorInfo[1]) && (int) $e->errorInfo[1] === 1062) {
        $_SESSION['error'] = 'This email is already registered.';
    }

    header('Location: register.php?action=rewrite');
    exit();
}

unset($_SESSION['join']);
$_SESSION['success'] = 'User created successfully. Please log in.';

header('Location: login.php');
exit();
