<?php
    include 'menu_ppal_maestros.php';
?>
<?php
include 'db.php';
$conn->set_charset("utf8"); //Para grantizar las tildes

$search_keyword = '';
if (!empty($_GET['search'])) {
    $search_keyword = $_GET['search'];
}

// Consulta con búsqueda
$query = "SELECT municipio.cod_municipio, municipio.nom_municipio, departamento.nom_departamento, pais.nom_pais 
          FROM municipio
          JOIN departamento ON municipio.cod_departamento = departamento.cod_departamento
          JOIN pais ON municipio.cod_pais = pais.cod_pais
          WHERE municipio.nom_municipio LIKE '%$search_keyword%' 
             OR departamento.nom_departamento LIKE '%$search_keyword%' 
             OR pais.nom_pais LIKE '%$search_keyword%'
          ORDER BY municipio.cod_municipio ASC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Municipios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Lista de Municipios</h2>

    <div class="row mb-4">
        <div class="col-md-6">
            <form action="form_consulta_mpio.php" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Buscar por municipio, departamento o país" value="<?= htmlspecialchars($search_keyword) ?>">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </div>
            </form>
        </div>
        <div class="col-md-6 text-end">
            <a href="create_mpio.php" class="btn btn-success">Agregar Municipio</a>
        </div>
    </div>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre Municipio</th>
                <th>Departamento</th>
                <th>País</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0) { ?>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?= $row['cod_municipio'] ?></td>
                        <td><?= $row['nom_municipio'] ?></td>
                        <td><?= $row['nom_departamento'] ?></td>
                        <td><?= $row['nom_pais'] ?></td>
                        <td>
                            <a href="update_mpio.php?cod_municipio=<?= $row['cod_municipio'] ?>" class="btn btn-primary btn-sm">Editar</a>
                        </td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="5" class="text-center">No se encontraron resultados</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
