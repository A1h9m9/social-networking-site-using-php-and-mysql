<?php
session_start();
if (isset($_SESSION['username'])) {
include 'connect.php';
$stmt = $con->prepare('SELECT * FROM image_page WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$row = $stmt->fetch();

if ($row) {
    $stmt = $con->prepare('DELETE FROM image_page WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    header('Location: timeline.php');
    exit;
} 
}
?>