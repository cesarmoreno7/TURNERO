<?php
    include 'menu_ppal_maestros.php';
?>
<?php
    include 'db.php';
    $conn->set_charset("utf8"); //Para grantizar las tildes
    
    $cod_empresa =  $_SESSION['cod_empresa'];
    
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $query = "SELECT servicio.servicio_id, servicio.nombre, empresas.nom_empresa 
              FROM servicio 
              JOIN empresas ON servicio.cod_empresa = empresas.cod_empresa
              WHERE empresas.cod_empresa = $cod_empresa  AND servicio.nombre LIKE ? OR empresas.nom_empresa LIKE ?";
              
    $stmt = $conn->prepare($query);
    $search_param = "%" . $search . "%";
    $stmt->bind_param('ss', $search_param, $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Color de fondo azul celeste para los encabezados de la tabla */
        thead th {
            background-color: #87CEEB; /* Azul celeste */
            color: white; /* Color del texto en blanco para contraste */
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <form method="GET" action="form_consulta_servicio.php" class="mb-4">
        <div class="row">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Buscar servicio" value="<?php echo $search; ?>">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
        </div>
    </form>

    <a href="create_servicio.php" class="btn btn-success mb-3">Agregar Servicio</a>
    <center><h2>Lista de Servicios</h2></center>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre del Servicio</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['servicio_id']; ?></td>
                    <td><?php echo $row['nombre']; ?></td>
                    <td>
                        <a href="update_servicio.php?id=<?php echo $row['servicio_id']; ?>" class="btn btn-warning">Editar</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
