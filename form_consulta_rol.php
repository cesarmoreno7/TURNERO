<?php
    include 'menu_ppal_maestros.php';
?>
<?php
include 'db.php';
$conn->set_charset("utf8"); //Para grantizar las tildes
$search = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT * FROM rol WHERE cod_rol LIKE ? OR cod_alterno_rol LIKE ? OR desc_rol LIKE ?";
$stmt = $conn->prepare($query);
$search_param = "%" . $search . "%";
$stmt->bind_param('sss', $search_param, $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Roles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Lista de Roles</h2>
    <form method="GET" action="form_consulta_rol.php" class="mb-3">
        <input type="text" name="search" class="form-control" placeholder="Buscar por código o descripción" value="<?php echo $search; ?>">
        <button type="submit" class="btn btn-primary mt-2">Buscar</button>
    </form>
    <a href="create_rol.php" class="btn btn-success mb-3">Agregar Rol</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Código</th>
                <th>Código Alterno</th>
                <th>Descripción</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['cod_rol']; ?></td>
                    <td><?php echo $row['cod_alterno_rol']; ?></td>
                    <td><?php echo $row['desc_rol']; ?></td>
                    <td>
                        <a href="update_rol.php?id=<?php echo $row['cod_rol']; ?>" class="btn btn-warning">Editar</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
