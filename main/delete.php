<?php
session_start();
require("../register/dbconnect.php");

if (!isset($_SESSION['id'])) {
    header('Location: ../register/login.php');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    header('Location: main.php');
    exit();
}
$id = (int) $_POST['id'];

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