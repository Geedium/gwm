<?php

namespace GWM\Core\Controllers {

    use GWM\Core\Response;
    use PHPMailer\PHPMailer\{Exception, PHPMailer, SMTP};

    /**
     * Class Mail
     * @package GWM\Core\Controllers
     * @version 1.0.0
     */
    class Mail
    {
        function Send(array $options = []): void
        {
            switch($options['pux.route'][3]['type']) {
                case 1:
                    $mail = new PHPMailer(true);

                    try {
                        $mail->SMTPDebug = SMTP::DEBUG_OFF;
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = $_ENV['MAIL_SMTP_USER'];
                        $mail->Password = $_ENV['MAIL_SMTP_PASS'];
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                        $mail->Port = 465;

                        $mail->setFrom($_ENV['MAIL_SMTP_USER'], 'Mailer');
                        //$mail->addAddress('joe@example.net', 'Joe User');
                        $mail->addAddress('geed0000czero@gmail.com');
                        //$mail->addReplyTo('info@example.com', 'Information');
                        //$mail->addCC('cc@example.com');
                        //$mail->addBCC('bcc@example.com');

                        // $mail->addAttachment('/var/tmp/file.tar.gz');
                        // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');

                        $mail->isHTML(true);
                        $mail->Subject = 'Here is the subject';
                        $mail->Body = 'This is the HTML message body <b>in bold!</b>';
                        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                        $mail->send();
                        echo 'Message has been sent';
                        exit;
                    } catch (Exception $e) {
                        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                    } catch (\Exception $e) {
                        echo "Message could not be sent. Error: {$e->getMessage()}";
                    }
                    break;
            }
            exit;
        }
    }
}