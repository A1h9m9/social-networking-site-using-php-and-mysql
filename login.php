<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login2.php');
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'connect.php';

    $username = $_POST['username'];
    $email = ($_POST['email']);
    $password = md5($_POST['password']);
    
    if (empty($username) || empty($password)) {
        // Display error message
        $error = 'Both username and password are required.';
    } else {
        // Use prepared statement to prevent SQL injection
        $stmt = $con->prepare('SELECT user_id, username,email, password FROM users WHERE username = ? AND email=? AND password = ?');
        $stmt->execute([$username, $email, $password]);
        $row = $stmt->fetch();

        if ($stmt->rowCount() > 0) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['user_id'] = $row['user_id'];
            header('Location: timeline.php');
            exit;
        } else {
            // Redirect with an error message if login fails
            $error = 'Invalid username or password';
        }
    }
}
?>

<!-- Your HTML form here -->


<div class="alert alert-danger" role="alert">
    <?php echo $error ?>
</div>