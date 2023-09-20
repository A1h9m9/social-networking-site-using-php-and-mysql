<?php
session_start();
if (isset($_SESSION['username'])) {
include 'connect.php';
$stmt = $con->prepare('SELECT * FROM image_page WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$row = $stmt->fetch();

if ($row) {
    header('Content-type: ' . $row['file_type']);
    echo $row['file_data'];
    
} else {
    echo "Image not found.";
}
}
?>