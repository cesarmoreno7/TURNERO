<?php
    include 'menu_ppal_maestros.php';
?>
<?php
include 'db.php';

$servicio_id = $_GET['id'];
$query = "SELECT * FROM servicio WHERE servicio_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $servicio_id);
$stmt->execute();
$result = $stmt->get_result();
$servicio = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $cod_empresa = $_POST['cod_empresa'];

    $update_query = "UPDATE servicio SET nombre = ? WHERE servicio_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param('si', $nombre, $servicio_id);

    if ($stmt->execute()) {
        echo "Servicio actualizado con éxito";
        header('Location: form_consulta_servicio.php');
    } else {
        echo "Error: " . $stmt->error;
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar Servicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Actualizar Servicio</h2>
    <form method="POST" action="update_servicio.php">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del Servicio</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $servicio['nombre']; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="form_consulta_servicio.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
