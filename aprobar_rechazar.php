<?php
    include("session.php");
    include 'db.php';
    include 'enviar_correo.php';
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        $intercambio_id = $_POST['intercambio_id'];
        $cod_empresa = $_SESSION['cod_empresa'];
        $codigo_usu  = $_SESSION['codigo_usu'];
        $servicio_id = $_SESSION['servicio_id']; 
        $observaciones = $_POST['observaciones']; 
        
        $accion = isset($_POST['aprobar']) ? 'Aprobar' : 'Rechazar';
    
        if ($accion == 'Aprobar') {
            // Obtener datos de la solicitud de intercambio
            $sql_intercambio = "SELECT * FROM intercambio_turnos WHERE id_intercambio = $intercambio_id AND servicio_id = $servicio_id AND cod_empresa = $cod_empresa";
            //echo $sql_intercambio;
            $result_intercambio = $conn->query($sql_intercambio);
            $intercambio = $result_intercambio->fetch_assoc();
    
            $empleado_id_solicitante = $intercambio['empleado_id_solicitante'];
            $empleado_id_receptor = $intercambio['empleado_id_receptor'];
            $fecha_turno = $intercambio['fecha_turno'];
            $turno_original = $intercambio['id_turno_original'];
            $turno_nuevo = $intercambio['id_turno_nuevo'];
            
            // Consulta SQL para obtener el detalle_id del solicitante
            $sql = "SELECT detalle_id FROM detalle_turno WHERE empleado_id = ? AND fecha = ? AND cod_empresa = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isi", $empleado_id_solicitante, $fecha_turno, $cod_empresa); // "isi" indica un entero y una cadena y un entero
            
            // Ejecutar la consulta
            $stmt->execute();
            $result = $stmt->get_result();
            
            // Verificar si se encontró un resultado
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $detalle_id_solicitante = $row['detalle_id'];
            }
            
            // Consulta SQL para obtener el detalle_id del receptor
            $sql = "SELECT detalle_id FROM detalle_turno WHERE empleado_id = ? AND fecha = ? AND cod_empresa = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isi", $empleado_id_receptor, $fecha_turno, $cod_empresa); // "isi" indica un entero y una cadena y un entero
            
            // Ejecutar la consulta
            $stmt->execute();
            $result = $stmt->get_result();
            
            // Verificar si se encontró un resultado
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $detalle_id_receptor = $row['detalle_id'];
            }
    
            // Actualizar los turnos en la tabla "detalle_turno"
            $sql_update_solicitante = "UPDATE detalle_turno SET empleado_id = $empleado_id_receptor WHERE detalle_id = $detalle_id_solicitante AND empleado_id = $empleado_id_solicitante AND fecha = '$fecha_turno' AND cod_empresa = $cod_empresa AND servicio_id = $servicio_id";   
            $sql_update_receptor = "UPDATE detalle_turno SET empleado_id = $empleado_id_solicitante WHERE detalle_id = $detalle_id_receptor AND empleado_id = $empleado_id_receptor AND fecha = '$fecha_turno' AND cod_empresa = $cod_empresa AND servicio_id = $servicio_id";   
            
            
            // Consulta SQL para obtener el id_turno del solicitante
            $sql = "SELECT id_turno FROM horas_turnos WHERE empleado_id = ? AND fecha = ? AND cod_empresa = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isi", $empleado_id_solicitante, $fecha_turno, $cod_empresa); // "isi" indica un entero y una cadena y un entero
            
            // Ejecutar la consulta
            $stmt->execute();
            $result = $stmt->get_result();
            
            // Verificar si se encontró un resultado
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $id_turno_solicitante = $row['id_turno'];
            }
            
            // Consulta SQL para obtener el id_turno del receptor
            $sql = "SELECT id_turno FROM horas_turnos WHERE empleado_id = ? AND fecha = ? AND cod_empresa = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isi", $empleado_id_receptor, $fecha_turno, $cod_empresa); // "isi" indica un entero y una cadena y un entero
            
            // Ejecutar la consulta
            $stmt->execute();
            $result = $stmt->get_result();
            
            // Verificar si se encontró un resultado
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $id_turno_receptor = $row['id_turno'];
            }
            
            // ACTUALIZAR EN LA TABLA: horas_turnos
            $sql_update_solicitant = "UPDATE horas_turnos SET empleado_id = $empleado_id_receptor WHERE id_turno = $id_turno_solicitante AND empleado_id = $empleado_id_solicitante AND fecha = '$fecha_turno' AND cod_empresa = $cod_empresa";
            $sql_update_recepto = "UPDATE horas_turnos SET empleado_id = $empleado_id_solicitante WHERE id_turno = $id_turno_receptor AND empleado_id = $empleado_id_receptor AND fecha = '$fecha_turno' AND cod_empresa = $cod_empresa";
    
            if ($conn->query($sql_update_solicitante) === TRUE && $conn->query($sql_update_receptor) === TRUE && $conn->query($sql_update_solicitant) === TRUE && $conn->query($sql_update_recepto) === TRUE) {
                
                // Actualizar el estado de la solicitud de intercambio
                $sql_update_estado = "UPDATE intercambio_turnos SET estado = 'Aprobado', observaciones = '$observaciones', fecha_aprobacion = NOW(), cod_usu_aprueba = '$codigo_usu' WHERE id_intercambio = $intercambio_id AND cod_empresa = $cod_empresa";
                $conn->query($sql_update_estado);
                
                // Actualizar los turnos en la tabla "detalle_turno_def"
                $sql_update_solicitante_def = "UPDATE detalle_turno_def SET empleado_id = $empleado_id_receptor WHERE detalle_id = $detalle_id_solicitante AND empleado_id = $empleado_id_solicitante AND fecha = '$fecha_turno' AND cod_empresa = $cod_empresa AND servicio_id = $servicio_id";   
                $conn->query($sql_update_solicitante_def);
                $sql_update_receptor_def = "UPDATE detalle_turno_def SET empleado_id = $empleado_id_solicitante WHERE detalle_id = $detalle_id_receptor AND empleado_id = $empleado_id_receptor AND fecha = '$fecha_turno' AND cod_empresa = $cod_empresa AND servicio_id = $servicio_id"; 
                $conn->query($sql_update_receptor_def);
                
                // ACTUALIZAR EN LA TABLA: horas_turnos_def
                $sql_update_solicitant_def = "UPDATE horas_turnos SET empleado_id = $empleado_id_receptor WHERE id_turno = $id_turno_solicitante AND  empleado_id = $empleado_id_solicitante AND fecha = '$fecha_turno' AND cod_empresa = $cod_empresa";
                $conn->query($sql_update_solicitant_def);
                $sql_update_recepto_def = "UPDATE horas_turnos SET empleado_id = $empleado_id_solicitante WHERE id_turno = $id_turno_receptor AND empleado_id = $empleado_id_receptor AND fecha = '$fecha_turno' AND cod_empresa = $cod_empresa";
                $conn->query($sql_update_recepto_def);
                
                // Obtener correos electrónicos de los empleados
                $sql_email_solicitante = "SELECT correo FROM empleados WHERE empleado_id = $empleado_id_solicitante AND cod_empresa = $cod_empresa";
                $email_solicitante = $conn->query($sql_email_solicitante);
                $result_intercambio_solic = $email_solicitante->fetch_assoc();
                $correo_id_solicitante = $result_intercambio_solic['correo'];
                
                $sql_email_receptor = "SELECT correo FROM empleados WHERE empleado_id = $empleado_id_receptor AND cod_empresa = $cod_empresa";
                $email_receptor = $conn->query($sql_email_receptor);
                $result_intercambio_recep = $email_receptor->fetch_assoc();
                $correo_id_receptor = $result_intercambio_recep['correo'];
    
                // Enviar correos electrónicos de notificación
                $subject = "Intercambio de turno aprobado";
                $message_solicitante = "Hola, tu solicitud de intercambio de turno para la fecha $fecha_turno ha sido aprobada. Tu nuevo turno es $turno_nuevo.";
                $message_receptor = "Hola, has recibido un intercambio de turno para la fecha $fecha_turno. Tu nuevo turno es $turno_original.";
                
                echo enviarCorreo($correo_id_solicitante, $message_solicitante, $subject,'form_consulta_intercambios_def.php');
                echo '<br>';
                echo enviarCorreo($correo_id_receptor, $message_receptor, $subject,'form_consulta_intercambios_def.php');
            } else {
                echo "Error: " . $conn->error;
            }
            
        } else {
            
            // Rechazar la solicitud de intercambio
            $sql_update_estado = "UPDATE intercambio_turnos SET estado = 'Rechazado', observaciones = '$observaciones', fecha_aprobacion = null, fecha_rechazo = NOW()  WHERE id_intercambio = $intercambio_id AND cod_empresa = $cod_empresa";
            if ($conn->query($sql_update_estado) === TRUE) {
                
                //echo "Intercambio de turno rechazado.";
                
                // Obtener correos electrónicos de los empleados
                $sql_email_solicitante = "SELECT correo FROM empleados WHERE empleado_id = $empleado_id_solicitante AND cod_empresa = $cod_empresa";
                $email_solicitante = $conn->query($sql_email_solicitante);
                $result_intercambio_solic = $result_intercambio->fetch_assoc();
                $correo_id_solicitante = $result_intercambio_solic['correo'];
                
                $sql_email_receptor = "SELECT correo FROM empleados WHERE empleado_id = $empleado_id_receptor AND cod_empresa = $cod_empresa";
                $email_receptor = $conn->query($sql_email_receptor);
                $result_intercambio_recep = $email_receptor->fetch_assoc();
                $correo_id_receptor = $result_intercambio_recep['correo'];
    
                // Enviar correos electrónicos de notificación
                $subject = "Intercambio de turno rechazado";
                $message_solicitante = "Hola, tu solicitud de intercambio de turno para la fecha $fecha_turno ha sido rechazado.";
                $message_receptor = "Hola, intercambio de turno para la fecha $fecha_turno ha sido rechazado.";
                
                echo enviarCorreo($correo_id_solicitante, $message_solicitante, $subject,'form_consulta_intercambios_def.php');
                echo '<br>';
                echo enviarCorreo($correo_id_receptor, $message_receptor, $subject,'form_consulta_intercambios_def.php');
                
            } else {
                echo "Error: " . $conn->error;
            }
        }
    }
    
    $conn->close();
?>
