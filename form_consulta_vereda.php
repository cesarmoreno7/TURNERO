<?php
    include 'menu_ppal_maestros.php';
?>
<?php
include 'db.php';
$conn->set_charset("utf8"); //Para grantizar las tildes

// Búsqueda
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
}

// Consulta de veredas con búsqueda
$query = "SELECT v.cod_vereda, v.nom_vereda, m.nom_municipio, d.nom_departamento, p.nom_pais 
          FROM vereda v
          JOIN municipio m ON v.cod_municipio = m.cod_municipio
          JOIN departamento d ON v.cod_departamento = d.cod_departamento
          JOIN pais p ON v.cod_pais = p.cod_pais
          WHERE v.nom_vereda LIKE '%$search_query%' 
          OR m.nom_municipio LIKE '%$search_query%' 
          OR d.nom_departamento LIKE '%$search_query%' 
          OR p.nom_pais LIKE '%$search_query%'";

$veredas_result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Veredas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Lista de Veredas</h2>
    
    <!-- Formulario de búsqueda -->
    <form method="GET" action="form_consulta_vereda.php" class="row g-3 mb-4">
        <div class="col-md-6">
            <input type="text" name="search" class="form-control" placeholder="Buscar por vereda, municipio, departamento o país" value="<?= $search_query ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Buscar</button>
        </div>
    </form>

    <a href="create_vereda.php" class="btn btn-success">Crear Nueva Vereda</a>
    
    <!-- Tabla de veredas -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Código Vereda</th>
                <th>Nombre Vereda</th>
                <th>Municipio</th>
                <th>Departamento</th>
                <th>País</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($veredas_result) > 0): ?>
                <?php while ($vereda = mysqli_fetch_assoc($veredas_result)): ?>
                    <tr>
                        <td><?= $vereda['cod_vereda'] ?></td>
                        <td><?= $vereda['nom_vereda'] ?></td>
                        <td><?= $vereda['nom_municipio'] ?></td>
                        <td><?= $vereda['nom_departamento'] ?></td>
                        <td><?= $vereda['nom_pais'] ?></td>
                        <td>
                            <a href="update_vereda.php?cod_vereda=<?= $vereda['cod_vereda'] ?>" class="btn btn-warning btn-sm">Editar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No se encontraron resultados</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
</div>

</body>
</html>
