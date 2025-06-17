<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__.'/../vendor/autoload.php';

function enviarCorreo($destinatario, $nombre_cliente, $asunto, $mensaje_body)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; 
        $mail->SMTPAuth = true;
        $mail->Username = 'nomadella_turismo@gmail.com'; 
        $mail->Password = 'nomadella_olimpiadas'; 
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('nomadella_turismo@gmail.com', 'Nomadella'); 
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
