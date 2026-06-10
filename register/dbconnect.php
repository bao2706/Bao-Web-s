<?php 
try{
$db = new PDO('mysql:host=localhost;dbname=test3;charset=utf8mb4', 'root', '');
} catch(PDOException $e) {
echo 'DB接続エラー:'.$e->getMessage();
}
?>