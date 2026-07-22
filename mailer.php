<?php
// Importamos las clases de PHPMailer al espacio de nombres global
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Llamamos a los archivos de la carpeta PHPMailer que descargaste
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

// Creamos una función maestra para enviar correos fácilmente
function enviarCorreo($destinatario, $asunto, $cuerpo) {
    $mail = new PHPMailer(true);

    try {
        // --------------------------------------------------------
        // 1. CONFIGURACIÓN DEL SERVIDOR SMTP (AQUÍ VAN TUS DATOS)
        // --------------------------------------------------------
        $mail->isSMTP();                                            // Usar SMTP
        $mail->Host       = 'smtp.gmail.com';                       // Servidor de Gmail
        $mail->SMTPAuth   = true;                                   // Habilitar autenticación
        
        // ¡OJO AQUÍ! Cambia esto por tus datos:
        $mail->Username   = 'joykerquintero1511@gmail.com';                  // El correo desde donde generaste la clave
        $mail->Password   = 'cpst fbfq tdte bach';       // Tu clave de aplicación (TODO PEGADO, SIN ESPACIOS)
        
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            // Encriptación segura
        $mail->Port       = 465;                                    // Puerto seguro de Gmail

        // --------------------------------------------------------
        // 2. REMITENTE Y DESTINATARIO
        // --------------------------------------------------------
        // Cambia 'TU_CORREO@gmail.com' por el tuyo. El texto 'Escuela de Formación Bíblica' es el nombre que verá el usuario.
        $mail->setFrom('joykerquintero1511@gmail.com', 'Escuela de Formación Bíblica');
        $mail->addAddress($destinatario);                           // A quién le llega (se llena automático)

        // --------------------------------------------------------
        // 3. FORMATO Y CONTENIDO DEL CORREO
        // --------------------------------------------------------
        $mail->isHTML(true);                                        // Permitir formato HTML (colores, negritas, links)
        $mail->CharSet    = 'UTF-8';                                // Para que lea bien las tildes y las ñ
        $mail->Subject    = $asunto;
        $mail->Body       = $cuerpo;

        // Enviar
        $mail->send();
        return true;
    } catch (Exception $e) {
        // Si hay un error, nos devuelve falso en lugar de tumbar la página
        return false;
    }
}
?>