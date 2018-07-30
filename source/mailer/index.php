<?php
require 'mail.php';
echo '<title>Messageboard-Email</title>';
$mail = new PHPMailer;
$mail->SMTPDebug = 0;
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'example@gmail.com';
$mail->Password = '';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;
$mail->CharSet = 'utf-8';
$mail->setFrom('example@gmail.com', 'Messageboard');
$mail->addAddress('member@gmail.com');
$mail->isHTML(true);

$mail->Subject = 'Messageboard';
$mail->Body    = '<h1>Messageboard</h1>';
$mail->AltBody = 'Messageboard';

if (!$mail->send()) {
    echo 'Email Sent Fail !';
    echo '<br />';
    echo 'Error Info：' . $mail->ErrorInfo;
} else {
    echo 'Email Sent Success！';
}
