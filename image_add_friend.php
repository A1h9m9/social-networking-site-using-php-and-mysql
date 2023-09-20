<?php
session_start();
if (isset($_SESSION['username'])) {
    include 'connect.php';
    $stmt = $con->prepare('SELECT users.user_id, image_page.image_id, image_page.file_type, image_page.file_data
    FROM image_page
    LEFT JOIN users ON users.user_id = image_page.id
    GROUP BY users.user_id');
    $stmt->execute();
    $row = $stmt->fetch();

    if ($row) {
        header('Content-type: ' . $row['file_type']);
        echo $row['file_data'];
    } else {
        echo "Image not found.";
    }
}
