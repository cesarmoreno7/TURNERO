<?php
    include("session.php");
    include 'db.php'; // Conexión a la base de datos
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $empleado_id = $_POST['empleado_id'];
        $fecha = $_POST['fecha'];
        $horas = $_POST['horas'];
    
        // Validar que los campos no estén vacíos
        if (!empty($empleado_id) && !empty($fecha) && isset($horas)) {
            // Comprobar si existe un registro en la tabla "detalle_turno"
            $check_query = "SELECT 1 FROM detalle_turno WHERE empleado_id = ? AND fecha = ?";
            $check_stmt = $conn->prepare($check_query);
            $check_stmt->bind_param('is', $empleado_id, $fecha);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
    
            if ($check_result->num_rows > 0) {
                // Si existe un registro, proceder con la actualización
                $query = "UPDATE detalle_turno 
                          SET hadic = ? 
                          WHERE empleado_id = ? AND fecha = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('iis', $horas, $empleado_id, $fecha);
    
                if ($stmt->execute()) {
                    echo "<script>alert('Horas adicionales actualizadas correctamente.'); window.location.href = 'tu_pagina.php';</script>";
                } else {
                    echo "<script>alert('Error al actualizar las horas adicionales: " . $conn->error . "'); window.history.back();</script>";
                }
            } else {
                // Si no existen registros, mostrar un mensaje
                echo "<script>alert('No existen registros en la tabla detalle_turno para actualizar.'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Por favor complete todos los campos.'); window.history.back();</script>";
        }
    }
?>
