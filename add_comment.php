<?php
session_start();
// make sure if username session Existing or not
if (isset($_SESSION['username'])) {
    // make connection form this page and database
    include 'connect.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment = $_POST['comment'];
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];

        // Insert a new like
        $stmt98 = $con->prepare('INSERT INTO comment(comment, id, post_id)VALUES(:comment, :id, :post_id)');
        $stmt98->bindParam(':comment', $comment,  PDO::PARAM_STR);
        $stmt98->bindParam(':id', $user_id,  PDO::PARAM_STR);
        $stmt98->bindParam(':post_id', $post_id,  PDO::PARAM_STR);
        $stmt98->execute();
        if ($stmt98->rowCount() > 0) {
            header("Location: timeline.php?id=" . $post_id);
            exit;
        }
    }
}