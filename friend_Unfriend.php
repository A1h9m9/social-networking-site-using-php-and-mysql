<?php
session_start();
// make sure if username session Existing or not
if (isset($_SESSION['username'])) {
    // make connection form this page and database
    include 'connect.php';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accept_friend'])) {
        $loggedInUserId = $_SESSION['username'];
        $stmt2 = $con->prepare('SELECT u.username
        FROM friend_requests AS fr
        INNER JOIN users AS u ON fr.sender_id = u.user_id
        WHERE fr.receiver_id = ?');
        $stmt2->execute([$loggedInUserId]);
        if ($stmt2->rowCount() > 0) {
            $stmt332 = $con->prepare('UPDATE friend_requests AS fr
            INNER JOIN users AS u ON fr.sender_id = u.user_id AND fr.receiver_id = ?
            SET fr.status = 0');
            $stmt332->execute([$loggedInUserId]);
            if ($stmt332->rowCount() > 0) {
                header("Location: timeline.php");
                exit;
            }
        }
    }
}