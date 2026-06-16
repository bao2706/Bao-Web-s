<?php
session_start();
require('../register/dbconnect.php');

if (!isset($_SESSION['id'])) {
    header('Location: ../register/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: main.php');
    exit();
}

$token = $_POST['csrf_token'] ?? '';

if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
    header('Location: main.php');
    exit();
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($id <= 0) {
    header('Location: main.php');
    exit();
}

$stmt = $db->prepare(
    'DELETE FROM income
     WHERE id = ?
       AND user_id = ?'
);
$stmt->execute([$id, (int) $_SESSION['id']]);

header('Location: main.php');
exit();
