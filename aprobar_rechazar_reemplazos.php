<?php
    include("session.php");
    include 'db.php';
    include 'enviar_correo.php';
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        $id_reemplazo = $_POST['id_reemplazo'];
        $cod_empresa = $_SESSION['cod_empresa'];
        $codigo_usu  = $_SESSION['codigo_usu'];
        $servicio_id = $_SESSION['servicio_id']; 
        $observaciones = $_POST['observaciones']; 
        
        $accion = isset($_POST['aprobar']) ? 'Aprobar' : 'Rechazar';
    
        if ($accion == 'Aprobar') {
            // Obtener datos de la solicitud de reemplazo
            $sql_reemplazo = "SELECT * FROM reemplazo WHERE id_reemplazo = $id_reemplazo AND servicio_id = $servicio_id AND cod_empresa = $cod_empresa";
            //echo $sql_reemplazo;
            $result_reemplazo = $conn->query($sql_reemplazo);
            $reemplazo = $result_reemplazo->fetch_assoc();
    
            $id_empleado_reemplaza   = $reemplazo['id_empleado_reemplaza'];
            $id_empleado_reemplazado = $reemplazo['id_empleado_reemplazado'];
            $fecha_ini_reemplazo     = $reemplazo['fecha_ini_reemplazo'];
            $fecha_fin_reemplazo     = $reemplazo['fecha_fin_reemplazo'];
    
            // Actualizar el empleado reemplazado en la tabla:"detalle_turno"
            $sql_update_reemplaza_dt = "UPDATE detalle_turno SET empleado_id = $id_empleado_reemplaza WHERE empleado_id = $id_empleado_reemplazado 
                                          AND fecha >= '$fecha_ini_reemplazo' AND fecha <= '$fecha_fin_reemplazo' AND cod_empresa = $cod_empresa AND servicio_id = $servicio_id";            
            
           // ACTUALIZAR el empleado reemplazado EN LA TABLA: horas_turnos
           $sql_update_reemplaza_ht = "UPDATE horas_turnos SET empleado_id = $id_empleado_reemplaza WHERE empleado_id = $id_empleado_reemplazado 
                                          AND fecha >= '$fecha_ini_reemplazo' AND fecha <= '$fecha_fin_reemplazo' AND cod_empresa = $cod_empresa"; 
           
    
            if ($conn->query($sql_update_reemplaza_dt) === TRUE && $conn->query($sql_update_reemplaza_ht) === TRUE) {
                
                // Actualizar el estado de la solicitud de reemplazo
                $sql_update_estado = "UPDATE reemplazo SET estado = 'Aprobado', observaciones = '$observaciones', fec_aprueba = NOW(), usu_aprueba = '$codigo_usu' WHERE id_reemplazo = $id_reemplazo AND cod_empresa = $cod_empresa";
                $conn->query($sql_update_estado);
                
                
                // Actualizar el empleado reemplazado en la tabla:"detalle_turno_def"
                $sql_update_reemplaza_dt_def = "UPDATE detalle_turno_def SET empleado_id = $id_empleado_reemplaza WHERE empleado_id = $id_empleado_reemplazado 
                                          AND fecha >= '$fecha_ini_reemplazo' AND fecha <= '$fecha_fin_reemplazo' AND cod_empresa = $cod_empresa AND servicio_id = $servicio_id";            
                $conn->query($sql_update_reemplaza_dt_def);
                
                // ACTUALIZAR el empleado reemplazado EN LA TABLA: horas_turnos_def
                $sql_update_reemplaza_ht_def = "UPDATE horas_turnos_def SET empleado_id = $id_empleado_reemplaza WHERE empleado_id = $id_empleado_reemplazado 
                                          AND fecha >= '$fecha_ini_reemplazo' AND fecha <= '$fecha_fin_reemplazo' AND cod_empresa = $cod_empresa";
                $conn->query($sql_update_reemplaza_ht_def);
    
                // Obtener correos electrónicos de los empleados
                $sql_email_solicitante = "SELECT correo FROM empleados WHERE empleado_id = $id_empleado_reemplaza AND cod_empresa = $cod_empresa";
                $email_solicitante = $conn->query($sql_email_solicitante);
                $result_reemplazo_solic = $email_solicitante->fetch_assoc();
                $correo_id_solicitante = $result_reemplazo_solic['correo'];
                
                $sql_email_receptor = "SELECT correo FROM empleados WHERE empleado_id = $id_empleado_reemplazado AND cod_empresa = $cod_empresa";
                $email_receptor = $conn->query($sql_email_receptor);
                $result_reemplazo_recep = $email_receptor->fetch_assoc();
                $correo_id_receptor = $result_reemplazo_recep['correo'];
    
                // Enviar correos electrónicos de notificación
                $subject = "Reemplazo de turno aprobado";
                $message_solicitante = "Hola, te ha sido asignado un reemplazo de turno para el rango de fechas $fecha_ini_reemplazo -- $fecha_fin_reemplazo";
                $message_receptor = "Hola, tu solicitud de reemplazo de turno para el rango de fechas: $fecha_ini_reemplazo -- $fecha_fin_reemplazo ha sido aprobada.";
                
                echo enviarCorreo($correo_id_solicitante, $message_solicitante, $subject,'form_consulta_reemplazos_def.php');
                echo '<br>';
                echo enviarCorreo($correo_id_receptor, $message_receptor, $subject,'form_consulta_reemplazos_def.php');
            } else {
                echo "Error: " . $conn->error;
            }
            
        } else {
            
            // Rechazar la solicitud de reemplazo
            $sql_update_estado = "UPDATE reemplazo SET estado = 'Rechazado', observaciones = '$observaciones', fec_aprueba = null, fecha_rechazo = NOW()
                                   WHERE id_reemplazo = $id_reemplazo AND cod_empresa = $cod_empresa";
            if ($conn->query($sql_update_estado) === TRUE) {
                
                //echo "reemplazo de turno rechazado.";
                
                // Obtener correos electrónicos de los empleados
                $sql_email_solicitante = "SELECT correo FROM empleados WHERE empleado_id = $id_empleado_reemplaza AND cod_empresa = $cod_empresa";
                $email_solicitante = $conn->query($sql_email_solicitante);
                $result_reemplazo_solic = $result_reemplazo->fetch_assoc();
                $correo_id_solicitante = $result_reemplazo_solic['correo'];
                
                $sql_email_receptor = "SELECT correo FROM empleados WHERE empleado_id = $id_empleado_reemplazado AND cod_empresa = $cod_empresa";
                $email_receptor = $conn->query($sql_email_receptor);
                $result_reemplazo_recep = $email_receptor->fetch_assoc();
                $correo_id_receptor = $result_reemplazo_recep['correo'];
    
                // Enviar correos electrónicos de notificación
                $subject = "Reemplazo de turno rechazado";
                
                $message_solicitante =  "Hola, el reemplazo de turno para el rango de fechas $fecha_ini_reemplazo -- $fecha_fin_reemplazo ha sido rechazado.";
                $message_receptor = "Hola, tu solicitud de reemplazo de turno para el rango de fechas $fecha_ini_reemplazo -- $fecha_fin_reemplazo ha sido rechazado.";
                
                echo enviarCorreo($correo_id_solicitante, $message_solicitante, $subject,'form_consulta_reemplazos_def.php');
                echo '<br>';
                echo enviarCorreo($correo_id_receptor, $message_receptor, $subject,'form_consulta_reemplazos_def.php');
                
            } else {
                echo "Error: " . $conn->error;
            }
        }
    }
    
    $conn->close();
?>
