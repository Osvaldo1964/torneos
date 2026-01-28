<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



class Email
{
    public static function sendEmail($data)
    {
        $mail = new PHPMailer(true);
        try {
            // Configuración del servidor
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USER;
            $mail->Password = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = SMTP_PORT;
            $mail->CharSet = 'UTF-8';

            // Destinatarios
            $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
            $mail->addAddress($data['email'], $data['nombre']);

            // Contenido
            $mail->isHTML(true);
            $mail->Subject = $data['asunto'];
            $mail->Body = $data['mensaje'];

            if (isset($data['adjunto']) && file_exists($data['adjunto'])) {
                $mail->addAttachment($data['adjunto'], $data['nombre_adjunto'] ?? 'archivo.pdf');
            }

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Error al enviar correo: " . $mail->ErrorInfo);
            return false;
        }
    }
}
