<?php
// ofertar_turno.php

// Conexión a la base de datos
$conn = new mysqli('localhost', 'prodsoft_turnero', 'turnero2024', 'prodsoft_turnero');

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener datos del formulario
$fecha_turno = $_POST['fecha_turno'];

// Valores de ejemplo para los demás campos
$empleado_id_solicitante = 1; // Cambiar por el valor adecuado
$empleado_id_receptor = NULL; // Cambiar por el valor adecuado
$estado_oferta = 'Pendiente'; // Cambiar según la lógica de tu aplicación
$estado_solicitud = 'Pendiente'; // Cambiar según la lógica de tu aplicación
$id_turno_nuevo = NULL; // Cambiar por el valor adecuado
$fecha_oferta = date('Y-m-d'); // Fecha actual
$fecha_aprobacion = NULL; // Inicialmente nulo
$cod_usu_aprueba = NULL; // Inicialmente nulo

// Consultar la tabla "detalle_turno" para obtener el "turno_id"
$sql_detalle = "SELECT turno_id FROM detalle_turno WHERE empleado_id = ? AND fecha = ?";
$stmt_detalle = $conn->prepare($sql_detalle);
$stmt_detalle->bind_param("is", $empleado_id_solicitante, $fecha_turno);
$stmt_detalle->execute();
$stmt_detalle->bind_result($id_turno_original);
$stmt_detalle->fetch();
$stmt_detalle->close();

if ($id_turno_original) {
    // Insertar datos en la tabla "oferta_turnos"
    $sql = "INSERT INTO oferta_turnos (empleado_id_solicitante, empleado_id_receptor, fecha_turno, estado_oferta, estado_solicitud, id_turno_original, id_turno_nuevo, fecha_oferta, fecha_aprobacion, cod_usu_aprueba)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisssiisss", $empleado_id_solicitante, $empleado_id_receptor, $fecha_turno, $estado_oferta, $estado_solicitud, $id_turno_original, $id_turno_nuevo, $fecha_oferta, $fecha_aprobacion, $cod_usu_aprueba);

    if ($stmt->execute()) {
        echo "Registro insertado correctamente";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Cerrar conexión
    $stmt->close();
} else {
    echo "No se encontró el turno original para el empleado y la fecha proporcionados.";
}

$conn->close();
?>