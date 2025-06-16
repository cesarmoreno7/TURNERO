<?php 
    include 'menu_ppal_maestros.php';
?>

<?php
    include 'db.php';
    $conn->set_charset("utf8"); //Para grantizar las tildes
    $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
    $query = "SELECT * FROM tipo_identificacion WHERE desc_tipo_id LIKE '%$searchTerm%' ORDER BY cod_tipo_id ASC";
    $result = $conn->query($query);
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD tipo identificaci&oacute;n</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Tipos de Identificaci&oacute;n</h2>

    <!-- Formulario de búsqueda -->
    <form method="GET" action="form_consulta_tipo_id.php" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Buscar tipo de identifiación..." value="<?php echo $searchTerm; ?>">
            <button type="submit" class="btn btn-primary">Buscar</button>
            <!-- <a href="index.php" class="btn btn-secondary">Limpiar</a> -->
        </div>
    </form>

    <!-- Botón para agregar país -->
    <a href="create_tipo_id.php" class="btn btn-success mb-3">Agregar Tipo de Identificaci&oacute;n</a>

    <!-- Tabla de países -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Código</th>
                <th>Descripci&oacute;n</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['cod_tipo_id']; ?></td>
                    <td><?php echo $row['desc_tipo_id']; ?></td>
                    <td>
                        <a href="update_tipo_id.php?cod_tipo_id=<?php echo $row['cod_tipo_id']; ?>" class="btn btn-warning">Editar</a>                        
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
