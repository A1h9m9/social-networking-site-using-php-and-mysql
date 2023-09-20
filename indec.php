<?php
require_once 'mail.php';
$mail->setFrom('deer.code.01@gmail.com', 'test');
$mail->addAddress('sadggg6@gmail.com');
$mail->Subject = 'test';
$mail->Body    = 'test <b>deercode</b>';
$mail->send();
echo 'Message has been sent';
