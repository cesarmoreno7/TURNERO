<?php
    include 'menu_ppal_maestros.php';
?>
<?php
    include("session.php");
    include 'db.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = $_POST['nombre'];
        $servicio_id = $_POST['servicio_id'];

        $cod_empresa = $_SESSION['cod_empresa'];

        $query = "INSERT INTO centro_costo (nombre, servicio_id, cod_empresa)
                VALUES ('$nombre', $servicio_id, $cod_empresa)";
        $conn->query($query);

        echo "Centro de Costo creado con éxito";
        header('Location: form_consulta_ccostos.php');
    }

    // Obtener servicios para el dropdown
    $servicios = $conn->query("SELECT * FROM servicio WHERE cod_empresa = $cod_empresa");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Centro de Costo</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Crear Centro de Costo</h2>
    <form method="POST" action = "create_ccosto.php">
        <div class="form-group">
            <label>Nombre</label>
            <input type="text" class="form-control" name="nombre" required>
        </div>
        <div class="form-group">
            <label>Servicio</label>
            <selct class="form-control" name="servicio_id" required>
                <option value="">Seleccione un Servicio</option>
                <?php while ($servicio = $servicios->fetch_assoc()): ?>
                    <option value="<?= $servicio['servicio_id'] ?>"><?= $servicio['nombre'] ?></option>
                <?php endwhile; ?>
            </selct>
        </div>        
        <button type="submit" class="btn btn-primary">Crear</button>
        <a href="form_consulta_ccostos.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
