<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa" crossorigin="anonymous"></script>
    <!--Stylesheet-->
</head>

<body>

</body>

</html>
<?php
include 'connect.php';

if (
    isset($_POST['username']) &&
    isset($_POST['email']) &&
    isset($_POST['password']) &&
    isset($_POST['name'])
) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $name = $_POST['name'];
    if (empty($username)) { ?>
        <div class="alert alert-danger" role="alert">
            A simple danger alert—check it out!
        </div>
    <?php
    } elseif (empty($email)) { ?>
        <div class="alert alert-danger" role="alert">
            A simple danger alert—check it out!
        </div>
    <?php
    } elseif (empty($password)) { ?>
        <div class="alert alert-danger" role="alert">
            A simple danger alert—check it out!
        </div>
    <?php
    } elseif (empty($name)) { ?>
        <div class="alert alert-danger" role="alert">
            A simple danger alert—check it out!
        </div>
    <?php
    } elseif (empty($email)) { ?>
        <div class="alert alert-danger" role="alert">
            A simple danger alert—check it out!
        </div>
        <?php
    } else {
        $stmt = $con->prepare('SELECT username FROM users WHERE username=?');
        $stmt->execute([$username]);
        if ($stmt->rowCount() > 0) { ?>
            <div class="alert alert-danger" role="alert">
                this username is taken
            </div>

<?php
        } else {
            $stmt = $con->prepare("INSERT INTO users (username ,email ,password ,full_name)VALUES(:zusername ,:zemail ,:zpassword, :zfull_name)");
            $stmt->execute(array(
                'zusername' => $username,
                'zpassword' => $password,
                'zfull_name' => $name,
                'zemail'     => $email,
            ));
            if ($stmt->rowCount() > 0) {
                header("Location:login2.php");
                exit;
            }
        }
    }
}





?>