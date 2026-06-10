<?php
session_start();
require("../register/dbconnect.php");

if (!isset($_SESSION['id'])) {
    exit();
}

$id = (int)$_GET['id'];

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