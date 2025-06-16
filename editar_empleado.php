<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("UPDATE empleados SET nombre = ?, apellido = ? WHERE empleado_id = ?");
    $stmt->bind_param('sssi', $_POST['nombre'], $_POST['apellido'],$_POST['empleado_id']);
    $stmt->execute();
    header('Location: form_consulta_empleados.php');
}
?>
