<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require APP_PATH . '/services/phpmailer/PHPMailer.php';
require APP_PATH . '/services/phpmailer/Exception.php';
require APP_PATH . '/services/phpmailer/SMTP.php';

class MailService extends PHPMailer
{
    public function __construct($config = null)
    {
        $this->isHTML(true);
        $this->isSMTP();
        $this->CharSet = $config['CharSet'];
        $this->Host = $config['Host'];
        $this->SMTPAuth = (bool)$config['SMTPAuth'];
        $this->SMTPSecure = $config['SMTPSecure'];
        $this->Username = $config['Username'];
        $this->Password = $config['Password'];
        $this->Port = (int)$config['Port'];
    }
    public function sendWithExceptions()
    {
        $this->exceptions = true; // Включаем исключения
        try {
            $this->send();
        } catch (Exception $e) {
            echo $e->errorMessage();
            die();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
    public function createToken()
    {

    }
}
