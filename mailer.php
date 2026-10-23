<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

function enviarCorreo($destinatario, $asunto, $cuerpo) {
    $mail = new PHPMailer(true);

    try {
        // 1. CONFIGURACIÓN DEL SERVIDOR SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        
        $mail->Username   = 'joykerquintero1511@gmail.com';
        
        // PEGA AQUÍ LA NUEVA CLAVE DE 16 LETRAS SIN ESPACIOS
        $mail->Password   = 'wyqu pszf yanz znnu'; 
        
        // Cambio a STARTTLS y puerto 587 para evitar bloqueos de SSL en Windows
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Opciones SSL para Localhost / XAMPP
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // 2. REMITENTE Y DESTINATARIO
        $mail->setFrom('joykerquintero1511@gmail.com', 'Escuela de Formación Bíblica');
        $mail->addAddress($destinatario);

        // 3. FORMATO Y CONTENIDO
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $asunto;
        $mail->Body    = $cuerpo;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return $mail->ErrorInfo;
    }
}
?>