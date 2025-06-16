<?php
    include("session.php");
    // Conexión a la base de datos
    include "db.php";
    
    $servicio_id = $_SESSION['servicio_id'];
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        $cod_empresa = $_SESSION['cod_empresa'];
        $empleado_id_solicitante = $_SESSION['empleado_id']; // Asumiendo que el ID del empleado está en la sesión
        $empleado_id_receptor = $_POST['empleado_id_receptor'];
        $fecha_turno = $_POST['fecha_turno'];
        $estado = 'Pendiente';
        $fecha_solicitud = date('Y-m-d');
    
        //echo $fecha_solicitud;  
        // Obtener el turno del solicitante en la fecha especificada
        $sql_turno_solicitante = "SELECT turno_id FROM detalle_turno WHERE empleado_id = $empleado_id_solicitante AND fecha = '$fecha_turno'";
        $result_solicitante = $conn->query($sql_turno_solicitante);
        $turno_solicitante = $result_solicitante->fetch_assoc();
        $turno_solicitante = $turno_solicitante['turno_id'];
        //echo $sql_turno_solicitante;  
        
        // Obtener el turno del receptor en la fecha especificada
        $sql_turno_receptor = "SELECT turno_id FROM detalle_turno WHERE empleado_id = $empleado_id_receptor AND fecha = '$fecha_turno'";
        $result_receptor = $conn->query($sql_turno_receptor);
        $turno_receptor = $result_receptor->fetch_assoc();
        $turno_receptor = $turno_receptor['turno_id'];
        
        // Insertar la solicitud de intercambio
        $sql_insert = "INSERT INTO intercambio_turnos (empleado_id_solicitante, empleado_id_receptor, fecha_turno, estado, id_turno_original, id_turno_nuevo,fecha_solicitud,fecha_aprobacion,cod_usu_aprueba,cod_empresa,servicio_id,fecha_rechazo)
                       VALUES ($empleado_id_solicitante, $empleado_id_receptor, '$fecha_turno', '$estado', '$turno_solicitante', '$turno_receptor','$fecha_solicitud',null,null,'$cod_empresa',$servicio_id,null)";
        //echo $sql_insert;               
        if ($conn->query($sql_insert) === TRUE) {
            echo "Solicitud de intercambio enviada.";
            header('Location: form_solicitar_intercambio_turno.php');
        } else {
            echo "Error: " . $conn->error;
        }
    }
    
    $conn->close();
?>
