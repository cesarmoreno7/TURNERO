<?php
    include 'menu_ppal_maestros.php';
?>
<?php
include 'db.php';
$conn->set_charset("utf8"); //Para grantizar las tildes

$cod_empresa =  $_SESSION['cod_empresa'];

$search = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT grupo.grupo_id, grupo.nombre, centro_costo.nombre AS centro_costo_nombre, empresas.nom_empresa, servicio.nombre AS servicio_nombre
          FROM grupo
          JOIN centro_costo ON grupo.centro_costo_id = centro_costo.centro_costo_id
          JOIN empresas ON grupo.cod_empresa = empresas.cod_empresa
          JOIN servicio ON grupo.servicio_id = servicio.servicio_id
          WHERE empresas.cod_empresa = $cod_empresa  AND grupo.nombre LIKE ? OR centro_costo.nombre LIKE ? OR empresas.nom_empresa LIKE ? OR servicio.nombre LIKE ?";
          
$stmt = $conn->prepare($query);
$search_param = "%" . $search . "%";
$stmt->bind_param('ssss', $search_param, $search_param, $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Grupos</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Lista de Grupos</h2>
    <form method="GET" action="form_consulta_grupo.php" class="form-inline mb-3">
        <input type="text" name="search" class="form-control mr-2 w-50" placeholder="Buscar por grupo, centro de costo, empresa o servicio" value="<?php echo $search; ?>">
        <button type="submit" class="btn btn-primary mt-2">Buscar</button>
    </form>
    <a href="create_grupo.php" class="btn btn-success mb-3">Agregar Grupo</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre del Grupo</th>
                <th>Centro de Costo</th>
                <th>Servicio</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['grupo_id']; ?></td>
                    <td><?php echo $row['nombre']; ?></td>
                    <td><?php echo $row['centro_costo_nombre']; ?></td>
                    <td><?php echo $row['servicio_nombre']; ?></td>
                    <td>
                        <a href="update_grupo.php?id=<?php echo $row['grupo_id']; ?>" class="btn btn-warning">Editar</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
