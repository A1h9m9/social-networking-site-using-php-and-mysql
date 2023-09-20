<?php
session_start();
if (isset($_SESSION['username'])) {
include 'connect.php';
if($_SERVER['REQUEST_METHOD']==='POST'){

if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK){
  $file_name = $_FILES['image']['name'];
  $file_type = $_FILES['image']['type'];
  $file_data = file_get_contents($_FILES['image']['tmp_name']);

  $stmt9=$con->prepare('INSERT INTO image_page(file_name, file_type, file_data, id)VALUES(:zfile_name, :zfile_type, :zfile_data,:zid)');
  $stmt9->execute(array(
      'zfile_name'=>$file_name,
      'zfile_type'=>$file_type,
      'zfile_data'=>$file_data,
      'zid' => $_SESSION['user_id']
  ));
  if($stmt9->rowCount()>0){
    header('Location: timeline.php');
    exit;
  }
  

}

}
}