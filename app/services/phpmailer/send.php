<?php
/* Namespace alias. */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '/services/phpmailer/PHPMailer.php';
require '/services/phpmailer/Exception.php';
require '/services/phpmailer/SMTP.php';

/* Create a new PHPMailer object. Passing TRUE to the constructor enables exceptions. */
$mail = new PHPMailer(TRUE);
/* Open the try/catch block. */
try {
    /* Set the mail sender. */
    $mail->setFrom('zed-n.v@mail.ru', 'ООО "Зумер"');
    /* Add a recipient. */
    $mail->addAddress('zed-n.v@mail.ru', 'Дмитрий');
    /* Set the subject. */
    $mail->Subject = 'Сброс пароля';
    /* Set the mail message body. */
    $mail->isHTML(TRUE);
    $mail->Body = '<html>Перейдите по ссылке для сброса пароля: <strong>Ссылка</strong>.</html>';
    $mail->AltBody = 'Перейдите по ссылке для сброса парооя: ссылка';

    /* SMTP parameters. */

    /* Tells PHPMailer to use SMTP. */
    $mail->isSMTP();

    /* SMTP server address. */
    $mail->Host = 'smtp.mail.ru';
    /* Use SMTP authentication. */
    $mail->SMTPAuth = TRUE;

    /* Set the encryption system. */
    $mail->SMTPSecure = 'tls';

    /* SMTP authentication username. */
    $mail->Username = 'zed-n.v@mail.ru';

    /* SMTP authentication password. */
    $mail->Password = '7dum9T4rcNrT1vtpnvNS';

    /* Set the SMTP port. */
    $mail->Port = 587;
    /* Finally send the mail. */
    $mail->send();
} catch (Exception $e) {
    /* PHPMailer exception. */
    echo $e->errorMessage();
} catch (\Exception $e) {
    /* PHP exception (note the backslash to select the global namespace Exception class). */
    echo $e->getMessage();
}
