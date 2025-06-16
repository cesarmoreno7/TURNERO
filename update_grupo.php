<?php
    include 'menu_ppal_maestros.php';
?>
<?php
include 'db.php';

$grupo_id = $_GET['id'];
$query = "SELECT * FROM grupo WHERE grupo_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $grupo_id);
$stmt->execute();
$result = $stmt->get_result();
$grupo = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $centro_costo_id = $_POST['centro_costo_id'];
    $cod_empresa = $_POST['cod_empresa'];
    $servicio_id = $_POST['servicio_id'];

    $update_query = "UPDATE grupo SET nombre = ?, centro_costo_id = ?, cod_empresa = ?, servicio_id = ? WHERE grupo_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param('siiii', $nombre, $centro_costo_id, $cod_empresa, $servicio_id, $grupo_id);

    if ($stmt->execute()) {
        echo "Grupo actualizado con éxito";
        header('Location: form_consulta_grupo.php');
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Obtener datos de centro_costo
$query_centro_costo = "SELECT centro_costo_id, nombre FROM centro_costo";
$result_centro_costo = $conn->query($query_centro_costo);

// Obtener datos de empresas
$query_empresas = "SELECT cod_empresa, nom_empresa FROM empresas";
$result_empresas = $conn->query($query_empresas);

// Obtener datos de servicio
$query_servicio = "SELECT servicio_id, nombre FROM servicio";
$result_servicio = $conn->query($query_servicio);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar Grupo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Actualizar Grupo</h2>
    <form method="POST" action="update_grupo.php">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del Grupo</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $grupo['nombre']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="centro_costo_id" class="form-label">Centro de Costo</label>
            <select class="form-select" id="centro_costo_id" name="centro_costo_id" required>
                <?php while ($row = $result_centro_costo->fetch_assoc()) { ?>
                    <option value="<?php echo $row['centro_costo_id']; ?>" <?php if ($row['centro_costo_id'] == $grupo['centro_costo_id']) echo 'selected'; ?>>
                        <?php echo $row['nombre']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="cod_empresa" class="form-label">Empresa</label>
            <select class="form-select" id="cod_empresa" name="cod_empresa" required>
                <?php while ($row = $result_empresas->fetch_assoc()) { ?>
                    <option value="<?php echo $row['cod_empresa']; ?>" <?php if ($row['cod_empresa'] == $grupo['cod_empresa']) echo 'selected'; ?>>
                        <?php echo $row['nom_empresa']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="servicio_id" class="form-label">Servicio</label>
            <select class="form-select" id="servicio_id" name="servicio_id" required>
                <?php while ($row = $result_servicio->fetch_assoc()) { ?>
                    <option value="<?php echo $row['servicio_id']; ?>" <?php if ($row['servicio_id'] == $grupo['servicio_id']) echo 'selected'; ?>>
                        <?php echo $row['nombre']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="form_consulta_grupo.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
