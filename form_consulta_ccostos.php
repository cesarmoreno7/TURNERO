<?php
    include 'menu_ppal_maestros.php';
?>
<?php
include("session.php");
include 'db.php';
$conn->set_charset("utf8"); //Para grantizar las tildes

// Inicializar variables para la búsqueda
$nombre = '';
$servicio_id = '';

$cod_empresa = $_SESSION['cod_empresa'];

// Verificar si hay una búsqueda activa
$whereClauses = [];
if (isset($_GET['buscar'])) {
    if (!empty($_GET['nombre'])) {
        $nombre = $_GET['nombre'];
        $whereClauses[] = "centro_costo.nombre LIKE '%$nombre%' AND cod_empresa = $cod_empresa";
    }
    if (!empty($_GET['servicio_id'])) {
        $servicio_id = $_GET['servicio_id'];
        $whereClauses[] = "centro_costo.servicio_id = $servicio_id AND cod_empresa = $cod_empresa";
    }
}

// Generar la consulta SQL con filtros
$query = "SELECT centro_costo.*, servicio.nombre AS servicio_nombre 
          FROM centro_costo
          JOIN servicio ON centro_costo.servicio_id = servicio.servicio_id";

// Agregar cláusulas WHERE si se han seleccionado filtros
if (count($whereClauses) > 0) {
    $query .= " WHERE " . implode(' AND ', $whereClauses);
}

$result = $conn->query($query);

// Obtener los servicios para el dropdown en la búsqueda
$servicios = $conn->query("SELECT * FROM servicio WHERE cod_empresa = $cod_empresa");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centros de Costo</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Centros de Costo</h2>
    
    <!-- Formulario de búsqueda -->
    <form method="GET" class="mb-4">
        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="nombre">Nombre</label>
                <input type="text" class="form-control" name="nombre" value="<?= $nombre ?>" placeholder="Nombre del centro de costo">
            </div>
            <div class="form-group col-md-3">
                <label for="servicio_id">Servicio</label>
                <select class="form-control" name="servicio_id">
                    <option value="">Seleccione un servicio</option>
                    <?php while ($servicio = $servicios->fetch_assoc()): ?>
                        <option value="<?= $servicio['servicio_id'] ?>" <?= $servicio_id == $servicio['servicio_id'] ? 'selected' : '' ?>>
                            <?= $servicio['nombre'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>            
        </div>
        <button type="submit" name="buscar" class="btn btn-primary">Buscar</button>
    </form>

    <a href="create_ccosto.php" class="btn btn-primary mb-3">Crear Centro de Costo</a>

    <!-- Tabla de resultados -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Servicio</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['centro_costo_id'] ?></td>
                        <td><?= $row['nombre'] ?></td>
                        <td><?= $row['servicio_nombre'] ?></td>
                        <td>
                            <a href="update_ccosto.php?id=<?= $row['centro_costo_id'] ?>" class="btn btn-warning">Editar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No se encontraron resultados</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
