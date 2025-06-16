<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    // Incluye las clases de PHPMailer
    require 'PHPMailer/PHPMailer/src/Exception.php';
    require 'PHPMailer/PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/PHPMailer/src/SMTP.php';

    function enviarCorreo($destinatario, $mensaje, $subject, $formulario) {
        $de = "pruebas@prodsoftsas.com"; 
        $nombreRemitente = "Cordialmente, Equipo de Pruebas";

        $mail = new PHPMailer(true);
        try {
            $mail->IsSMTP();
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = "ssl";
            $mail->Host = "snap.wnkserver8.com";
            $mail->Port = 465;
            $mail->From = $de;
            $mail->FromName = $nombreRemitente;
            $mail->Username = "pruebas@prodsoftsas.com"; // Usuario SMTP
            $mail->Password = "pruebas2024"; // Contraseña SMTP

            $mail->AddAddress($destinatario);
            $mail->IsHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $mensaje;
            $mail->WordWrap = 50;
            $mail->MsgHTML($mensaje);

            // Envío del correo
            if ($mail->send()) {
                echo "<script>
                        alert('¡Operación exitosa, se le ha enviado un correo, por favor verifique!');
                        window.location.href = '$formulario';
                      </script>";
                //return "Correo enviado exitosamente.";
            } else {
                return "Error al enviar el correo: " . $mail->ErrorInfo;
            }
        } catch (Exception $e) {
            return "Error al enviar el correo: " . $e->getMessage();
        }
    }
?>
