<?php
session_start();
// make sure if username session Existing or not
if (isset($_SESSION['username'])) {
    // make connection form this page and database
    include 'connect.php';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_friend'])) {
        $receiverId = $_POST['receiver_id'];
        $loggedInUserId = $_SESSION['user_id'];
        $stmt2 = $con->prepare('SELECT * FROM friend_requests WHERE sender_id = ? AND receiver_id = ?');
        $stmt2->execute([$loggedInUserId, $receiverId]);
        if ($stmt2->rowCount() === 0) {
            $stmt332 = $con->prepare('INSERT INTO friend_requests (sender_id, receiver_id) VALUES (?,?)');
            $stmt332->execute([$loggedInUserId, $receiverId]);
            if ($stmt332->rowCount() > 0) {
                header("Location: timeline.php?username=" . $receiverId);
                exit;
        }
    }
}
}
