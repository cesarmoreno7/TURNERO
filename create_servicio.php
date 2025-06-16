<?php
    include 'menu_ppal_maestros.php';
?>
<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $cod_empresa = $_POST['cod_empresa'];

    $query = "INSERT INTO servicio (nombre, cod_empresa) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $nombre, $cod_empresa);

    if ($stmt->execute()) {
        echo "Servicio creado con éxito";
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
    <title>Crear Servicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Crear Servicio</h2>
    <form method="POST" action="create_servicio.php">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del Servicio</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="form_consulta_servicio.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
