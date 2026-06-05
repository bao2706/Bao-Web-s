<?php 
require('./dbconnect.php');
session_start();
if (!isset($_SESSION['join'])) {
    header('Location: index.php');
    exit();
}
if (
    empty($_SESSION['join']['name']) ||
    empty($_SESSION['join']['email']) ||
    empty($_SESSION['join']['password'])
) {
    header('Location: index.php');
    exit();
}
$name = trim($_SESSION['join']['name']);
$email = trim($_SESSION['join']['email']);
$password = password_hash(trim($_SESSION['join']['password']), PASSWORD_DEFAULT);

try {
    $stmt = $db->prepare("INSERT INTO members (name, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $password]);
} catch (PDOException $e) {
    if ($e->errorInfo[1] == 1062) {
        echo "Email đã tồn tại";
    } else {
        echo "DB error: " . $e->getMessage();
    }
}
header('Location: login.php?create=1');
exit();
?>
