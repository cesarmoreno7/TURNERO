<?php
header('Content-Type: application/json');

// Conexión a la base de datos
$conn = new mysqli('localhost', 'prodsoft_turnero', 'turnero2024', 'prodsoft_turnero');
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$id_turno = $_POST['id_turno'];
$sql = "SELECT tipo_turno FROM turnos WHERE id_turno = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id_turno);
$stmt->execute();
$result = $stmt->get_result();
$response = $result->fetch_assoc();

echo json_encode($response);

$stmt->close();
$conn->close();
?>
