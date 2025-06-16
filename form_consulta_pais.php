<?php 
    include 'menu_ppal_maestros.php';
?>

<?php
    include 'db.php';
    $conn->set_charset("utf8"); //Para grantizar las tildes
    $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
    $query = "SELECT * FROM pais WHERE nom_pais LIKE '%$searchTerm%' ORDER BY cod_pais ASC";
    $result = $conn->query($query);
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD de Países</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Países</h2>

    <!-- Formulario de búsqueda -->
    <form method="GET" action="form_consulta_pais.php" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Buscar país..." value="<?php echo $searchTerm; ?>">
            <button type="submit" class="btn btn-primary">Buscar</button>
            <!-- <a href="index.php" class="btn btn-secondary">Limpiar</a> -->
        </div>
    </form>

    <!-- Botón para agregar país -->
    <a href="create_pais.php" class="btn btn-success mb-3">Agregar País</a>

    <!-- Tabla de países -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre del País</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['cod_pais']; ?></td>
                    <td><?php echo $row['nom_pais']; ?></td>
                    <td>
                        <a href="update_pais.php?cod_pais=<?php echo $row['cod_pais']; ?>" class="btn btn-warning">Editar</a>                        
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
