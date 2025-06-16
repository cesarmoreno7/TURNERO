<?php

    include("session.php");
    include 'db.php';

    $id = $_GET['id'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = $_POST['nombre'];
        $servicio_id = $_POST['servicio_id'];

        
        $query = "UPDATE centro_costo SET nombre = '$nombre'
                WHERE centro_costo_id = $id";
        $conn->query($query);

        echo "Centro de Costo actualizado con éxito";
            header('Location: form_consulta_ccostos.php');
    }

    // Obtener datos del centro de costo
    $query = "SELECT * FROM centro_costo WHERE centro_costo_id = $id";
    $result = $conn->query($query);
    $centro_costo = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Centro de Costo</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Editar Centro de Costo</h2>
    <form method="POST" action="update_ccosto.php">
        <div class="form-group">
            <label>Nombre</label>
            <input type="text" class="form-control" name="nombre" value="<?= $centro_costo['nombre'] ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="form_consulta_ccostos.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
