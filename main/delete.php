<?php
session_start();
require("../register/dbconnect.php");

if (!isset($_SESSION['id'])) {
    header('Location: ../register/login.php');
    exit();
}
if (isset($_POST['id'])) {
    $id = (int) $_POST['id'];
} elseif (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
} else {
    header('Location: main.php');
    exit();
}

if ($id <= 0) {
    header('Location: main.php');
    exit();
}

$stmt = $db->prepare("
DELETE FROM income
WHERE id = ?
AND user_id = ?
");

$stmt->execute([
    $id,
    $_SESSION['id']
]);

header("Location: main.php");
exit();