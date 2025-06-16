<?php
include("session.php");
include 'db.php';

$servicio_id = $_SESSION['servicio_id'];
$cod_empresa =  $_SESSION['cod_empresa'];

// Obtener el ID del servicio desde la solicitud GET
$centro_costo_id = isset($_GET['centro_costo_id']) ? intval($_GET['centro_costo_id']) : 0;

// Verificar que se haya enviado un centro de costos válido
if ($servicio_id > 0) {
    // Preparar la consulta segura usando prepared statements
    $query = $conn->prepare("SELECT grupo_id, nombre FROM grupo WHERE centro_costo_id = ? AND servicio_id = ? AND cod_empresa = ?");
    $query->bind_param('iii', $centro_costo_id,$servicio_id,$cod_empresa);
    $query->execute();
    $result = $query->get_result();

    // Generar las opciones del select
    echo '<option value="">Seleccione un grupo</option>';
    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . $row['grupo_id'] . '">' . htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') . '</option>';
    }

    $query->close();
} else {
    // Si no se proporciona un grupo válido
    echo '<option value="">No se encontraron Grupos</option>';
}
$conn->close();
?>