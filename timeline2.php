<?php
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    include 'connect.php';
    $post = $_POST['post'];
    $id = $_POST['id'];
    $stmt = $con->prepare("INSERT INTO posts(post,id)VALUES(:zpost, :zid)");
    $stmt->execute(array(
    'zpost' => $post,
    'zid' => $id,
));
if ($stmt->rowCount() > 0) {
    header("Location:timeline.php");
    exit;
}
}



