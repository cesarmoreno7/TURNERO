<?php 
    include 'menu_ppal_maestros.php';
?>
<?php
    include 'db.php';
    $conn->set_charset("utf8"); //Para grantizar las tildes

    $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
    $query = "SELECT d.cod_departamento, d.nom_departamento, p.nom_pais 
            FROM departamento d 
            JOIN pais p ON d.cod_pais = p.cod_pais
            WHERE d.nom_departamento LIKE '%$searchTerm%' ORDER BY d.cod_departamento ASC";
    $result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD de Departamentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Departamentos</h2>

    <!-- Formulario de búsqueda -->
    <form method="GET" action="form_consulta_depto.php" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Buscar departamento..." value="<?php echo $searchTerm; ?>">
            <button type="submit" class="btn btn-primary">Buscar</button>
            <!-- <a href="index.php" class="btn btn-secondary">Limpiar</a>-->
        </div>
    </form>

    <!-- Botón para agregar departamento -->
    <a href="create_depto.php" class="btn btn-success mb-3">Agregar Departamento</a>

    <!-- Tabla de departamentos -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre del Departamento</th>
                <th>País</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['cod_departamento']; ?></td>
                    <td><?php echo $row['nom_departamento']; ?></td>
                    <td><?php echo $row['nom_pais']; ?></td>
                    <td>
                        <a href="update_depto.php?cod_departamento=<?php echo $row['cod_departamento']; ?>" class="btn btn-warning">Editar</a>                        
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
