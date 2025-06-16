<?php
include 'db.php';

// Obtener el ID del servicio desde la solicitud
$servicio_id = $_POST['servicio_id'];

// Consulta para obtener los grupos filtrados por el servicio
$query = "SELECT grupo_id, nombre FROM grupo WHERE servicio_id = '$servicio_id'";
$result = $conn->query($query);

// Generar las opciones del select
echo '<option value="">Seleccione un grupo</option>';
while ($row = $result->fetch_assoc()) {
    echo '<option value="' . $row['grupo_id'] . '">' . $row['nombre'] . '</option>';
}
?>
