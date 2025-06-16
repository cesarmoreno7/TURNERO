<?php
	 $de = "pruebas@prodsoftsas.com"; 
	 $para = "cesarmoreno7@gmail.com";
	
	 $cabeceras = "MIME-Version: 1.0\r\n";
	 $cabeceras .= "Content-type: text/html; charset=iso-8859-1\r\n";
	 $cabeceras .= "From: $de\r\n";
	 
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;
	
	/*Clase para tratar con excepciones y errores*/
	require 'PHPMailer/PHPMailer/src/Exception.php';
	/*Clase PHPMailer*/
	require 'PHPMailer/PHPMailer/src/PHPMailer.php';
	/*Clase SMTP necesaria para la conexión con un servidor SMTP*/
	require 'PHPMailer/PHPMailer/src/SMTP.php';
	 
	 $mail = new PHPMailer(true);
	 $mail->IsSMTP();
	 $mail->SMTPAuth = true;
	 $mail->SMTPSecure = "ssl";
	 $mail->Host = "snap.wnkserver8.com";
	 $mail->Port = 465;
	 $mail->From = $de; // Define el correo del remitente correctamente
	 $mail->FromName = "Cordialmente, Equipo de Pruebas"; // Define el nombre del remitente
	 
	 $mensaje = "Hola, este es un mensaje de prueba";
	 
	 $mail->AddAddress($para); // Agrega la dirección del destinatario principal
	 //$mail->AddBCC($mail_admin, "Prueba"); // Agrega la dirección en copia oculta
	 
	 $mail->Username = "pruebas@prodsoftsas.com"; // Usuario SMTP
	 $mail->Password = "pruebas2024"; // Contraseña SMTP
	 
	 $mail->IsHTML(true);
	 $mail->Subject = "Mail de prueba"; // Asunto del correo
	 $mail->Body = $mensaje;
	 $mail->WordWrap = 50;
	 $mail->MsgHTML($mensaje);
	 
	 // Verifica si el correo fue enviado correctamente
	 if ($mail->send()) {
		 echo "Correo enviado exitosamente.";
	 } else {
		 echo "Error al enviar el correo: " . $mail->ErrorInfo;
	 }
?>
