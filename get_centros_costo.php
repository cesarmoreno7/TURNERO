<?php
include("session.php");
include 'db.php';

$cod_empresa =  $_SESSION['cod_empresa'];

// Obtener el ID del servicio desde la solicitud GET
$servicio_id = isset($_GET['servicio_id']) ? intval($_GET['servicio_id']) : 0;

// Verificar que se haya enviado un servicio_id válido
if ($servicio_id > 0) {
    // Preparar la consulta segura usando prepared statements
    $query = $conn->prepare("SELECT centro_costo_id, nombre FROM centro_costo WHERE servicio_id = ? AND cod_empresa = ?");
    $query->bind_param('ii', $servicio_id,$cod_empresa);
    $query->execute();
    $result = $query->get_result();

    // Generar las opciones del select
    echo '<option value="">Seleccione un centro de costo</option>';
    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . $row['centro_costo_id'] . '">' . htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') . '</option>';
    }

    $query->close();
} else {
    // Si no se proporciona un servicio_id válido
    echo '<option value="">No se encontraron centros de costo</option>';
}
$conn->close();
?>

