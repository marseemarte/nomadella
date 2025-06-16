<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__.'/../vendor/autoload.php';

function enviarCorreo($destinatario, $nombre_cliente, $asunto, $mensaje_body)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Cambia esto por el host de tu proveedor
        $mail->SMTPAuth = true;
        $mail->Username = 'nomadella_turismo@gmail.com'; // Tu correo real
        $mail->Password = 'nomadella_olimpiadas'; // Tu contraseña o app password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('nomadella_turismo@gmail.com', 'Nomadella'); // Cambia el remitente
        $mail->addAddress($destinatario, $nombre_cliente);
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body = $mensaje_body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Error al enviar email: {$mail->ErrorInfo}");
        return false;
    }
}
?>
