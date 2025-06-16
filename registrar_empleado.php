<?php
include 'db.php';
$cod_empresa = $_POST['cod_empresa']
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("INSERT INTO empleados (nombre, apellido, cod_empresa) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $_POST['nombre'], $_POST['apellido'], $cod_empresa);
    $stmt->execute();
    header('Location: form_consulta_empleados.php');
}
?>
